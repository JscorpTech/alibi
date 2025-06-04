<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use PHPUnit\TextUI\Help;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserFilter $filter)
    {
        $data = [];

        if ($filter->has('role')) {
            $role = $filter->get('role');
            if ($role != 'all') {
                $data['role'] = $role;
            }
        }

        if ($filter->has('phone')) {
            $phone = Helper::clearPhone($filter->get('phone'));
            if (preg_match('/^[0-9]{12}/', $phone)) {
                $data['phone'] = $phone;
            }
        }

        $users = User::query()->where($data)
            ->where('full_name', 'like', '%' . $filter->get('full_name') . '%')
            ->orderByDesc('id')
            ->paginate(Env::get('PAGE_SIZE'));

        return Response::view('admin.user.list', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrField($id);

        return Response::view('admin.user.detail', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrField($id);
        $user->delete();

        return Redirect::back();
    }
}
