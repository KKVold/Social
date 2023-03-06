<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Media;
use App\Models\PageUser;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public static function validate_files_extension($files_array)
    {
        $valied_files_extension = ['MP4', 'WMV', 'PNG', 'JPEG', 'JPG'];
        foreach ($files_array as $file) {
            $is_valid = false;
            foreach ($valied_files_extension as $extension) {
                if (strtolower($extension) == strtolower($file->getClientOriginalExtension())) {
                    $is_valid = true;
                }
            }
            if (!$is_valid) {
                return false;
            }
        }
        return true;
    }

    public static function is_photo($file)
    {
        $photo_extension = ['PNG', 'JPEG', 'JPG'];
        foreach ($photo_extension as $extension) {
            if (strtolower($extension) == strtolower($file->getClientOriginalExtension())) {
                return true;
            }
        }
        return false;
    }

    public static function delete_media($media_id)
    {
        if (!Media::where('media_id', '=', $media_id)->exists()) {
            return false;
        }
        $media = Media::find($media_id);
        if (!File::exists(public_path() . $media->media_path)) {
            return false;
        }
        File::delete(public_path() . $media->media_path);
        Media::find($media_id)->delete();
        return true;
    }
    public function delete(Request $request, $media_id)
    {
        $user = auth()->user();
        //check the media id
        if (!Media::where('media_id', '=', $media_id)->exists()) {
            return response()->json(
                [
                    'message' => 'no such media id'
                ],
                400
            );
        }
        $media = Media::find($media_id);
        // check if this user is the owner of this media
        if ($user->user_id != $media->post->auther->user_id) {
            return response()->json(
                [
                    'message' => 'you do not have permessions to delete this media'
                ],
                403
            );
        }
        // all is good try to delete this media
        //something went wrong maybe this path is invalid
        if (!$this->delete_media($media_id)) {
            return response()->json(
                [
                    'message' => 'something went wrong on our end, this media has been lost'
                ],
                503 // service unavailable
            );
        }
        return response()->json(
            [
                'message' => 'media has been deleted successfully'
            ],
            200
        );
    }
    public static function validate_user_can_get_media(Request $request, $media_id)
    {
        // this function will return true if a user can see the requested media
        $media = Media::find($media_id);
        // validate that this user can see this media
        $post = $media->post;
        $user = auth()->user();
        // if this media is in a group then the user should be a member in this group
        if ($post->group_id) {
            if (
                !GroupUser::where([
                    ['user_id', '=', $user->user_id],
                    ['group_id', '=', $post->group_id],
                    ['role_id', '>', 3] // limited access id is 3 
                ])->exists()
                && Group::find($post->group_id)->user_id != $user->user_id
            ) {
                return false;
            }
        }
        // if this media is on a public post then check if this user is blocked
        if ($post->page_id) {
            if (PageUser::where([
                ['user_id', '=', $user->user_id],
                ['page_id', '=', $post->post_id],
                ['role_id', '=', 4]
            ])->exists()) {
                return false;
            }
        }
        // check if the owner has been blocked this user
        if (Friend::whereIn('first_user_id', [$user->user_id, $post->user_id])
            ->whereIn('second_user_id', [$user->user_id, $post->user_id])
            ->where('status', '&', 3)->exists()
        ) {
            return false;
        }
        return true;
    }
    // get media using it's id
    public function get_media(Request $request, $media_id)
    {
        // check if this media exists
        if (!Media::where('media_id', '=', $media_id)->exists()) {
            return response()->json(
                [
                    'messsage' => 'no such media'
                ],
                400
            );
        }

        if (!$this->validate_user_can_get_media($request, $media_id)) {
            return response()->json(
                [
                    'message' => 'you do not have permission to see this media!'
                ],
                403
            );
        }
        // ok return the response
        return response()->json(
            [
                'message' => 'ok',
                'media' => Media::find($media_id)
            ],
            200
        );
    }
    public function get_post_media(Request $request, $post_id)
    {
        //check if this post exists
        if (!Post::where('post_id', '=', $post_id)->exists()) {
            return response()->json(
                [
                    'message' => 'this post does not exists'
                ],
                400
            );
        }
        //check if this post has media
        if (!Media::where('post_id', '=', $post_id)->exists()) {
            return response()->json(
                [
                    'message' => 'this post does not have media'
                ],
                400
            );
        }
        // check if this user can see the media
        if (!$this->validate_user_can_get_media($request, Media::where('post_id', '=', $post_id)->first()->media_id)) {
            return response()->json(
                [
                    'message' => 'you do not have permessions to see this post\'s media'
                ],
                403
            );
        }
        // ok return the media 
        return response()->json(
            [
                'message' => 'ok',
                'media' => Media::where('post_id', '=', $post_id)->get()
            ],
            200
        );
    }
}
