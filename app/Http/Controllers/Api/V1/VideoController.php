<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VideoLikeRequest;
use App\Http\Resources\Api\VideoResource;
use App\Models\Video;
use App\Models\VideoLike;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    public function index()
    {
        return VideoResource::paginate(Video::inRandomOrder()->paginate(10));
    }

    public function like(VideoLikeRequest $request){
        $video_id = $request->input("video_id");
        $like = VideoLike::query()->where(['video_id'=>$video_id,"user_id"=>Auth::user()->id]);
        if ($like->exists()){
            $like->delete();
            return $this->success("removed");
        }else{
            VideoLike::query()->create([
                "video_id"=>$video_id,
                "user_id"=>Auth::user()->id
            ]);
            return $this->success("created");
        }
    }
}
