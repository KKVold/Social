<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Friend;
use App\Models\GroupUser;
use App\Models\Media;
use App\Models\PageUser;
use App\Models\ReactionComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CommentController extends Controller
{
    // validate that a user can see the comment required
    public static function validate_user_can_see_comment($user_id, $comment_id)
    {
        if (!User::where('user_id', '=', $user_id)->exists()) {
            return false;
        }
        $user = User::find($user_id);
        if (!Comment::where('comment_id', '=', $comment_id)->exists()) {
            return false;
        }
        $comment = Comment::find($comment_id);
        //get the post of this comment
        $post = $comment->post;
        // check if this post is on a group
        if ($post->group) {
            // check if this user is a member in this group
            if (
                !GroupUser::where([
                    ['group_id', '=', $post->group_id],
                    ['user_id', '=', $user_id],
                    ['role_id', '<', '4'] // if the role's id is 4 then this user has been blocked from the group
                ])->exists()
                && !$post->group->owner->user_id != $user_id
            ) {
                return false;
            }
        }

        // check if this post is on a public page
        if ($post->page) {
            // check if this user has been blocked from this group
            if (PageUser::where([
                ['page_id', '=', $post->page_id],
                ['user_id', '=', $user_id],
                ['role_id', '=', 4] // if the role's id is 4 then this user is blocked from this page
            ])->exists()) {
                return false;
            }
        }
        // check if the owner has bolked this user
        if (Friend::whereIn('first_user_id', [$user->user_id, $comment->user_id])
            ->whereIn('second_user_id', [$user->user_id, $comment->user_id])
            ->where('status', '&', 3)->exists()
        ) {
            return false;
        }
        return true;
    }


    // add a comment
    public function add_comment(Request $request)
    {
        $validattion = Validator::make(
            $request->all(),
            [
                'post_id' => 'required|exists:posts,post_id',
                'parent_comment_id' => 'exists:comments,comment_id|nullable',
                'content' => 'nullable',
                'media' => 'file|nullable'
            ]
        );
        // return an error message if some error occures
        if ($validattion->fails()) {
            return response()->json([
                "message" => $validattion->errors()
            ], 400);
        }
        // if no content and no media return an error message
        if (!$request->media && !$request->content) {
            return response()->json([
                "message" => "you must provide a content or media"
            ], 400);
        }
        // check if the file extinsion is valid
        if ($request->media && !MediaController::is_photo($request->media)) {
            return response()->json([
                "message" => "invalid file extinsion"
            ], 400);
        }
        // if there is a media with this comment then add the path to the request
        if ($request->media != null) {
            $media_name = time() . $request->media->getClientOriginalName();
            $request->media->move('upload/photos/posted_photos/', $media_name);
            $request->media = '/upload/photos/posted_photos/' . $media_name;
        }
        $level = 0;
        //if there is a parent to this comment then increse the level by 1
        if ($request->parent_comment_id) {
            $level = min(10, Comment::find($request->parent_comment_id)->level);
        }
        $comment = [
            'post_id' => $request->post_id,
            'parrent_comment_id' => $request->parent_comment_id,
            'user_id' => auth()->user()->user_id,
            'content' => $request->content,
            'media_path' => $request->media,
            'level' => $level
        ];
        // check if this user can post this comment on this post if so then creat the comment and return the response
        if (!PostController::validate_user_can_see_post(auth()->user()->user_id, $request->post_id)) {
            return response()->json(
                [
                    "message" => "you do not have permessions to post a comment on this post"
                ],
                403
            );
        }
        $comment = Comment::create($comment);
        return response()->json(
            [
                "message" => "your comment has been created successfully",
                "comment" => $comment
            ],
            201
        );
    }

    public static function delete_media_attached_to_comment($comment_id)
    {
        if (!Comment::where('comment_id', '=', $comment_id)->exists()) {
            return false;
        }
        $media = Comment::find($comment_id)->media_path;
        if (!File::exists(public_path() . $media)) {
            return false;
        }
        File::delete(public_path() . $media);
        return true;
    }

    public function update_comment(Request $request, $comment_id)
    {
        $validattion = Validator::make(
            $request->all(),
            [
                'content' => 'nullable',
                'media' => 'file|nullable'
            ]
        );
        // return an error message if some error occures
        if ($validattion->fails()) {
            return response()->json([
                "message" => $validattion->errors()
            ], 400);
        }
        $user = auth()->user();
        // check if this comment is exists
        if (!Comment::where('comment_id', '=', $comment_id)->exists()) {
            return response()->json(
                [
                    "message" => "no such comment"
                ],
                400
            );
        }
        // get the comment
        $comment = Comment::find($comment_id);
        // check if this user is the owner of the comment 
        if ($user->user_id != $comment->user_id) {
            return response()->json(
                [
                    "message" => "you do not have permessions to update this comment"
                ],
                403
            );
        }
        // check if there is no content an no media
        if (!$comment->media_path && !$request->content && !$request->media) {
            return response()->json(
                [
                    "message" => "invalid update, your comment will be empty"
                ],
                400
            );
        }
        // validate the file in the request
        if ($request->media) {
            if (!MediaController::is_photo($request->media)) {
                return response()->json(
                    [
                        "message" => "invalid file format"
                    ],
                    400
                );
            }
            // check if there is an old media
            if ($comment->media_path) {
                CommentController::delete_media_attached_to_comment($comment_id);
            }
            $media_name = time() . $request->media->getClientOriginalName();
            $request->media->move('upload/photos/posted_photos/', $media_name);
            $comment->media_path = '/upload/photos/posted_photos/' . $media_name;
        }
        $comment->content = $request->content;
        Comment::where('comment_id', '=', $comment_id)->update($comment);
        // all is good return the response
        return response()->json(
            [
                "message" => "your comment has been updated successfully",
                "comment" => $comment

            ],
            200
        );
    }

    public static function deleteSingleComment($comment_id)
    {
        //this function assume that you have made all validation you want
        if (!Comment::where('comment_id', '=', $$comment_id)->exists()) {
            return null;
        }
        $comment = Comment::find($comment_id);
        if ($comment->media_path) {
            CommentController::delete_media_attached_to_comment($comment_id);
        }
        Comment::where('comment_id', '=', $comment_id)->delete();
        ReactionComment::where('comment_id', '=', $comment_id)->delete();
        return $comment;
    }
    public static function delete_comment($comment_id)
    {
        // this function assume that you have made all validations you want 
        // This is a DFS function will delete all the subtree of comments stating from this comment
        if (!Comment::where('comment_id', '=', $$comment_id)->exists()) {
            return false;
        }
        $comment = Comment::find($comment_id);
        // need to make BFS from this comment to delete all it's replaies 
        if (Comment::where("parent_comment_id", '=', $comment_id)->exists()) {
            $children = Comment::where("parent_comment_id", '=', $comment_id)->get();
            foreach ($children as $child) {
                CommentController::delete_comment($child->comment_id);
            }
        }
        CommentController::deleteSingleComment($comment_id);
        return true;
    }
    public function delete(Request $request, $comment_id)
    {
        $user = auth()->user();
        // check if this comment is exists
        if (!Comment::where('comment_id', '=', $comment_id)->exists()) {
            return response()->json(
                [
                    "message" => "no such comment"
                ],
                400
            );
        }
        // get the comment
        $comment = Comment::find($comment_id);
        // if this user is the owner then delete the comment
        if ($user->user_id == $comment->user_id) {
            CommentController::delete_comment($comment_id);
            return response()->json(
                [
                    "message" => "your comment has been deleted successfully"
                ],
                200
            );
        }
        $post = $comment->post;
        // check if this post is on a group and this user is an admin, and the owner of this comment is not the owner of the group
        if ($post->group && $post->group->user_id != $comment->user_id) {
            if (
                (GroupUser::where([
                    ['group_id', '=', $post->group_id],
                    ['user_id', '=', $user->user_id],
                    ['role_id', '=', 1] // if the role id is 1 then he is an admin
                ])->exists() &&
                    !GroupUser::where([
                        ['group_id', '=', $post->group_id],
                        ['user_id', '=', $comment->user_id],
                        ['role_id', '=', 1] // if the role id is 1 then he is an admin
                    ])->exists()
                )
                ||
                $post->group->user_id == $user->user_id
            ) {
                CommentController::delete_comment($comment_id);
                return response()->json(
                    [
                        "message" => "your comment has been deleted successfully"
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        "message" => "you do not have permessions to delete this comment"
                    ],
                    403
                );
            }
        }
        // check if this comment is on a pags's post 
        if ($post->page && $comment->user_id != $post->page->user_id) {
            if ((PageUser::where([
                    ['page_id', '=', $post->page_id],
                    ['user_id', '=', $user->user_id],
                    ['role_id', '=', 1] // an Admin
                ])->exists()
                    && !PageUser::where([
                        ['page_id', '=', $post->page_id],
                        ['user_id', '=', $comment->user_id],
                        ['role_id', '=', 1] // an Admin
                    ])->exists()
                ) ||
                $user->user_id == $post->page->user_id
            ) {
                CommentController::delete_comment($comment_id);
                return response()->json(
                    [
                        "message" => "your comment has been deleted successfully"
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        "message" => "you do not have permessions to delete this comment"
                    ],
                    403
                );
            }
        }
        // check if this post is posted on a private account
        if (!$post->page && !$post->group) {
            if ($user->user_id == $post->user_id) {
                CommentController::delete_comment($comment_id);
                return response()->json(
                    [
                        "message" => "your comment has been deleted successfully"
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        "message" => "you do not have permessions to delete this comment"
                    ],
                    403
                );
            }
        }
        return response()->json(
            [
                "message" => "you do not have permessions to delete this comment"
            ],
            403
        );
    }
}
