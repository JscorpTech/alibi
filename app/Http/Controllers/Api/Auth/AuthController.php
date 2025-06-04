<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ConfirmRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResendRequest;
use App\Http\Requests\Api\ResetRequest;
use App\Http\Requests\Api\SetPasswordRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\ResetConfirmRequest;
use App\Http\Resources\Api\MeResource;
use App\Models\PendingUser;
use App\Models\User;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Kreait\Firebase\Factory;

class AuthController extends Controller
{
    use BaseController;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['me', 'setPassword']);
    }

    /**
     * Resend otp code
     *
     * @param ResendRequest $request
     * @return JsonResponse
     * @response array{success:true,data:array{provider:"telegram|sms"}}
     */
    public function resend(ResendRequest $request): JsonResponse
    {
        try {
            $res = SmsService::sendConfirm($request->get('phone'));

            return $this->success(__('sms.send:success'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Register new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @response array{success:true}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        $full_name = $request->input('full_name');
        $password = $request->input('password');
        $fcm_token = $request->input('fcm_token');

        if (User::query()->where(['phone'=>$phone])->count() == 1){
            return Response::json([
                'status'  => false,
                'message' => "",
                'data'    => [
                    "phone"=>"The phone has already been taken."
                ],
                'code'    => 403,
            ]);
        }

        try {
            SmsService::sendConfirm($phone);

            PendingUser::query()->updateOrCreate(['phone' => $phone], [
                'phone'     => $phone,
                'full_name' => $full_name,
                'password'  => Hash::make($password),
                'fcm_token' => $fcm_token,
            ]);

            return Response::json([
                'status'  => true,
                'message' => __('sms.send:success'),
                'data'    => [],
                'code'    => 200,
            ]);
        } catch (\Throwable $e) {
            return Response::json([
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => [],
                'code'    => 403,
            ]);
        }
    }

    /**
     * Confirm otp code
     *
     * Telefon raqamni tasdiqlash uchun
     *
     * @param ConfirmRequest $request
     * @return JsonResponse
     */
    public function confirm(ConfirmRequest $request): JsonResponse
    {
        $code = $request->input('code');
        $phone = $request->input('phone');

        try {
            $res = SmsService::checkConfirm($phone, $code);
            if ($res) {
                $pending_user = PendingUser::query()->where(['phone' => $phone])->first();
                $user = User::query()->firstOrCreate(['phone' => $phone], [
                    'phone'     => $pending_user->phone,
                    'full_name' => $pending_user->full_name,
                    'password'  => $pending_user->password,
                    'fcm_token' => $pending_user->fcm_token,
                ]);

                $user->verified_at = Carbon::now();
                $user->save();
                $token = $user->createToken(Carbon::now()->format('d.m.Y H:i'))->plainTextToken;
                $pending_user->delete();
                try {
                    $factory = (new Factory())->withServiceAccount(base_path('firebase.json'));
                    $messaging = $factory->createMessaging();
                    $messaging->subscribeToTopic('allDevices', $user->fcm_token);
                } catch (\Throwable $e) {
                    Log::error($e);
                }

                return $this->success(
                    message: __('sms.confirm'),
                    data: [
                        'token' => $token,
                    ]
                );
            } else {
                return $this->error(__('invalid:error'));
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @response array{success:true,data:array{token:string}}
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::query()->where(['phone' => $phone])->first();
        $fcm_token = $request->input('fcm_token');

        if ($user?->password == null or !Hash::check($password, $user->password)) {
            return $this->error(__('invalid:password'));
        }

        if ($request->has('fcm_token')) {
            $user->update(['fcm_token' => $fcm_token]);
        }

        try {
            $factory = (new Factory())->withServiceAccount(base_path('firebase.json'));
            $messaging = $factory->createMessaging();
            $messaging->subscribeToTopic('allDevices', $fcm_token);
        } catch (\Throwable $e) {
            Log::error($e);
        }

        $token = $user->createToken('Base')->plainTextToken;

        return $this->success(data: [
            'token' => $token,
        ]);
    }

    /**
     * Get user data
     *
     * @return JsonResponse
     * @response MeResource
     */
    public function me(): JsonResponse
    {
        return $this->success(data: MeResource::make(Auth::user()));
    }

    /**
     * Update User profile
     *
     * @param UserUpdateRequest $request
     * @return JsonResponse
     * @response array{success:true,message:string}
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->fill($request->validated());
            $user->save();

            return $this->success(__('user.update:profile'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Reset user password
     *
     * Agar parolni unutgan bo'lsa tiklash uchun
     *
     * @param ResetRequest $request
     * @return JsonResponse
     * @response array{success:true,code:int,data:array{provider:'telegram|sms'}}
     */
    public function reset(ResetRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        try {
            $res = SmsService::sendConfirm($phone);

            return $this->success(__('sms.send:success'), data: [
                'provider' => $res == 'telegram' ? $res : 'sms',
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Reset password confirm
     *
     * @param ResetConfirmRequest $request
     * @return JsonResponse
     * @response array{success:true,message:string}
     */
    public function resetConfirm(ResetConfirmRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $password = $request->input('password');

        try {
            $check = SmsService::checkConfirm($phone, $code);
            if ($check == true) {
                User::query()->where(['phone' => $phone])->update(['password' => Hash::make($password)]);

                return $this->success(__('reset:password:done'));
            }

            return $this->error(__('reset:password:error'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Edit password
     *
     * @param SetPasswordRequest $request
     * @return JsonResponse
     * @response array{success:true,message:string}
     */
    public function setPassword(SetPasswordRequest $request): JsonResponse
    {
        $password = $request->input('password');
        try {
            $user = Auth::user();
            $user->password = Hash::make($password);
            $user->save();

            return $this->success(__('set:password:done '));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updateFcm(Request $request): JsonResponse
    {
        try {
            $factory = (new Factory())->withServiceAccount(base_path('firebase.json'));
            $messaging = $factory->createMessaging();
            $messaging->subscribeToTopic('allDevices', $request->input('fcm_token'));
        } catch (\Throwable $e) {
            Log::error($e);
        }

        return $this->success(__('updated:fcm'), data: ['token' => $request->input('fcm_token')]);
    }
}
