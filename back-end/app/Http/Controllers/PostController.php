<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Friend;
use Illuminate\Http\Request;
//use APP\Models\Post;
use APP\Models\Page;
use App\Models\Group;
use APP\Models\PageUser;
use App\Models\GroupUser;
use App\Models\Media;
use App\Models\Post;
use App\Models\ReactionComment;
use App\Models\ReactionPost;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use App\Models\TagPost;
use App\Models\TagUser;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public static function validate_user_can_see_post($user_id, $post_id)
    {
        if (!User::where('user_id', '=', $user_id)->exists()) {
            return false;
        }
        $user = User::find($user_id);
        // check if this post exists
        if (!Post::where('post_id', '=', $post_id)->exists()) {
            return false;
        }
        $post = Post::find($post_id);
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
        if (Friend::whereIn('first_user_id', [$user->user_id, $post->user_id])
            ->whereIn('second_user_id', [$user->user_id, $post->user_id])
            ->where('status', '&', 3)->exists()
        ) {
            return false;
        }
        return true;
    }
    public function validate_tags_array($tags)
    {
        $valid_tags = Tag::all();
        foreach ($tags as $tag) {
            $is_valid = false;
            foreach ($valid_tags as $valid_tag) {
                if ($valid_tag->name == $tag) {
                    $is_valid = true;
                }
            }
            if (!$is_valid) {
                return false;
            }
        }
        return true;
    }


    public function publish_post(Request $request)
    {
        $validattion = Validator::make(
            $request->all(),
            [
                'page_id' => 'exists:pages,page_id|nullable',
                'group_id' => 'exists:groups,group_id|nullable',
                'content' => 'nullable',
                'tags' => 'array',
                'media' => 'nullable'
            ]
        );
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }

        // make sure that the tags provided by the client are valid
        if (!PostController::validate_tags_array($request->tags)) {
            return response()->json(
                [
                    'message' => 'invalid tags'
                ],
                400
            );
        }
        if (!MediaController::validate_files_extension($request->media)) {
            return response()->json(
                [
                    'message' => 'invalid file extention'
                ],
                400
            );
        }
        $user = auth()->user();
        if ($request->page_id && $request->group_id) {
            return response()->json(
                [
                    'message' => 'you can not publish a post in a group and a page in the same time'
                ],
                400
            );
        }

        if ($request->page_id) {
            $page = Page::find($request->page_id);
            // check if this user can post on this page
            if (
                !PageUser::where([
                    ['page_id', '=', $page->page_id],
                    ['user_id', '=', $user->user_id],
                    ['role_id', '=', 1] // the admin role's id is 1 
                ])->exists() &&
                $page->user_id != $user->user_id
            ) {
                return response()->json(
                    [
                        'message' => 'you do not have permission to post on this page, you must be an admin'
                    ],
                    403 // forbidden
                );
            }
        } else if ($request->group_id) {
            $group = Group::find($request->group_id);
            if (
                !GroupUser::where(
                    [
                        ['group_id', '=', $group->group_id],
                        ['user_id', '=', $user->user_id],
                        ['role_id', '>', 3] // the limited access role's id is 3
                    ]
                )->exists() &&
                $group->user_id != $user->user_id
            ) {
                return response()->json(
                    [
                        'message' => 'you do not have permission to post in this group'
                    ],
                    403 // forbidden
                );
            }
        }
        // create a new post
        $post = Post::create(
            [
                'content' => $request->content,
                'page_id' => $request->page_id,
                'group_id' => $request->group_id,
                'user_id' => $user->user_id
            ]
        );
        // in this array we will store the post's tags with their IDs to return it wi the response
        $post_tags = [];
        $media = [];
        if ($post != null) {
            foreach ($request->tags as $tag) {
                TagPost::create([
                    'post_id' => $post->post_id,
                    'tag_id' => Tag::where('name', '=', $tag)->first()->tag_id
                ]);
                $post_tags[] = Tag::where('name', '=', $tag)->first();
            }
            //create an array with media info

            foreach ($request->media as $media_file) {
                $media_name = time() . $media_file->getClientOriginalName();
                if (MediaController::is_photo($media_file)) {
                    $media_file->move('upload/photos/posted_photos/', $media_name);
                    $media[] = Media::create([
                        'post_id' => $post->post_id,
                        'media_path' => '/upload/photos/posted_photos/' . $media_name,
                        'is_photo' => 1
                    ]);
                } else {
                    $media_file->move('upload/vedios/', $media_name);
                    $media[] = Media::create([
                        'post_id' => $post->post_id,
                        'media_path' => '/upload/vedios/' . $media_name,
                        'is_photo' => 0
                    ]);
                }
            }
        }
        // adding tags to the tag_users table
        foreach ($post_tags as $tag) {
            if (!TagUser::where([
                ['tag_id', '=', $tag->tag_id],
                ['user_id', '=', $user->user_id]
            ])->exists()) {
                TagUser::create([
                    'user_id' => $user->user_id,
                    'tag_id' => $tag->tag_id
                ]);
            }
        }
        // return the response with all info about the post
        return response()->json(
            [
                'message' => 'your post hase been posted successfully',
                'post' => $post,
                'post_tags' => $post_tags,
                'media' => $media
            ],
            201 // created
        );
    }

    public function show_post(Request $request, $post_id)
    {
        $user = auth()->user();
        // check if this post exists
        if (!Post::where('post_id', '=', $post_id)->exists()) {
            return response()->json(
                [
                    'message' => 'this post does not exists'
                ],
                404 //not found
            );
        }
        $post = Post::find($post_id);
        // check if the post is in a specific group and this user is not a member there
        if ($post->group_id) {
            if (
                !GroupUser::where([
                    ['user_id', '=', $user->user_id],
                    ['group_id', '=', $post->group_id],
                    ['role_id', '>', 3] // limited access id is 3 
                ])->exists()
                && Group::find($post->group_id)->user_id != $user->user_id
            ) {
                return response()->json(
                    [
                        'message' => 'you do not have permission to see this post!'
                    ],
                    403
                );
            }
        }
        // check if this post is posted on a public page and this user is blocked 
        if ($post->page_id) {
            if (PageUser::where([
                ['user_id', '=', $user->user_id],
                ['page_id', '=', $post->post_id],
                ['role_id', '=', 4]
            ])->exists()) {
                return response()->json(
                    [
                        'message' => 'you do not have permission to see this post!'
                    ],
                    403
                );
            }
        }
        // check if the owner of the post has been blocked this user 
        if (Friend::whereIn('first_user_id', [$user->user_id, $post->user_id])
            ->whereIn('second_user_id', [$user->user_id, $post->user_id])
            ->where('status', '&', 3)->exists()
        ) {
            return response()->json(
                [
                    'message' => 'you do not have permission to see this post!'
                ],
                403
            );
        }
        // all is good return the respons
        return response()->json(
            [
                'message' => "ok",
                'post' => $post,
                'media' => $post->media()->get(),
                'tags' => Tag::wherein('tag_id', $post->tag_posts()->get('tag_id'))->get()
            ],
            200
        );
    }
    public function update_post(Request $request, $post_id)
    {
        $user = auth()->user();
        // check if this post exists
        if (!Post::where('post_id', '=', $post_id)->exists()) {
            return response()->json(
                [
                    'message' => 'this post does not exists'
                ],
                404 //not found
            );
        }
        //the user can only update content, tags or add more media to the post

        $validattion = Validator::make(
            $request->only('content', 'tags', 'media'),
            [
                'content' => 'nullable',
                'tags' => 'array',
                'media' => 'nullable'
            ]
        );
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }
        // check if this user is the owner of this post
        $post = Post::find($post_id);
        if ($user->user_id != $post->user_id) {
            return response()->json(
                [
                    "message" => 'you do not have permission to update this post!'
                ],
                403
            );
        }
        // make sure that the tags provided by the client are valid
        if (!PostController::validate_tags_array($request->tags)) {
            return response()->json(
                [
                    'message' => 'invalid tags'
                ],
                400
            );
        }
        // make sure that these file are valid
        if (!MediaController::validate_files_extension($request->media)) {
            return response()->json(
                [
                    'message' => 'invalid file extention'
                ],
                400
            );
        }
        $post_tags = [];
        $media = [];
        //delete the old post tags 
        TagPost::where('post_id', '=', $post->post_id)->delete;
        //insert the new tags
        if ($post != null) {
            foreach ($request->tags as $tag) {
                TagPost::create([
                    'post_id' => $post->post_id,
                    'tag_id' => Tag::where('name', '=', $tag)->first()->tag_id
                ]);
                $post_tags[] = Tag::where('name', '=', $tag)->first();
            }
            //create an array with media info
            foreach ($request->media as $media_file) {
                $media_name = time() . $media_file->getClientOriginalName();
                if (MediaController::is_photo($media_file)) {
                    $media_file->move('upload/photos/posted_photos/', $media_name);
                    $media[] = Media::create([
                        'post_id' => $post->post_id,
                        'media_path' => '/upload/photos/posted_photos/' . $media_name,
                        'is_photo' => 1
                    ]);
                } else {
                    $media_file->move('upload/vedios/', $media_name);
                    $media[] = Media::create([
                        'post_id' => $post->post_id,
                        'media_path' => '/upload/vedios/' . $media_name,
                        'is_photo' => 0
                    ]);
                }
            }
        }
        // adding tags to the tag_users table
        foreach ($post_tags as $tag) {
            if (!TagUser::where([
                ['tag_id', '=', $tag->tag_id],
                ['user_id', '=', $user->user_id]
            ])->exists()) {
                TagUser::create([
                    'user_id' => $user->user_id,
                    'tag_id' => $tag->tag_id
                ]);
            }
        }
        // update the content of the post
        $post->content = $request->content;
        // update in the DB
        Post::find($post->post_id)->update($post);
        $post = Post::find($post->post_id);
        // return the respone 
        return response()->json(
            [
                'message' => 'your post has been updated successfully',
                'post' => $post,
                'media' => $post->media()->get(),
                'tags' => Tag::wherein('tag_id', $post->tag_posts()->get('tag_id'))->get()
            ],
            200
        );
    }

    public function show_tag_posts(Request $request, $tag_id)
    {
        if (!Tag::where('tag_id', '=', $tag_id)->exists()) {
            return response()->json(
                [
                    'message' => 'so such tag'
                ],
                400
            );
        }
        $posts = [];
        foreach (TagPost::where('tag_id', '=', $tag_id)->get() as $post) {
            $posts[] = PostController::show_post($request, $post->post_id)->original;
        }
        return response()->json(
            [
                'message' => 'success',
                'posts' => $posts
            ],
            200
        );
    }
    public function show_user_posts(Request $request, $user_id)
    {
        // check if this user exists
        if (!User::where('user_id', '=', $user_id)->exists()) {
            return response()->json(
                [
                    'message' => 'this user does not exists'
                ],
                404
            );
        }
        // check if the user has been blocked this user
        if (Friend::whereIn('first_user_id', [$user_id, auth()->user()->user_id])
            ->whereIn('second_user_id', [$user_id, auth()->user()->user_id])
            ->where('status', '&', 3)->exists()
        ) {
            return response()->json(
                [
                    'message' => 'you do not have permissions to see this page'
                ],
                403
            );
        }

        $posts = [];
        foreach (Post::where([
            ['user_id', '=', $user_id],
            ['group_id', '=', null],
            ['page_id', '=', null]
        ])->get() as $post) {
            $posts[] = PostController::show_post($request, $post->post_id)->original;
        }
        return response()->json(
            [
                'message' => 'success',
                'posts' => $posts
            ],
            200
        );
    }
    public static function delete_post($post_id)
    {
        // this function assume that you have made the validation required for this operation
        // this function will simply delete the post using it's id
        // delete post_tags
        TagPost::where('post_id', '=', $post_id)->delete();

        // delete post's media
        $post_media = Media::where('post_id', '=', $post_id)->get();
        foreach ($post_media as $media) {
            // use the declared function to delete media
            Media::delete_media($media->media_id);
        }
        //delte the comments related to this post
        $comments = Comment::where('post_id', '=', $post_id)->get();
        //delete the media and reactions related to this comment first
        foreach ($comments as $comment) {
            ReactionComment::where('comment_id', '=', $comment->comment_id)->delete();
            // check if this comment has a media file
            if ($comment->media_path != null) {
                if (File::exists(public_path() . $comment->media_path)) {
                    File::delete(public_path() . $comment->media_path);
                }
            }
        }
        // delete reactions related to this post
        ReactionPost::where('post_id', '=', $post_id)->delete();
        // delete the comments
        Comment::where('post_id', '=', $post_id)->delete();
        // delete the post it self
        Post::find($post_id)->delete();
    }
    public function delete(Request $request, $post_id)
    {
        $user = auth()->user();
        // check if this post exists
        if (!Post::where('post_id', '=', $post_id)->exists()) {
            return response()->json(
                [
                    'message' => 'no such post'
                ],
                400
            );
        }
        // get the post using it's id
        $post = Post::find($post_id);

        // if the user is the owner of the post then delete it 
        if ($post->auther->user_id == $user->user_id) {
            $this->delete_post($post_id);
            return response()->json(
                [
                    'message' => 'the post has been deleted successflly'
                ],
                200
            );
        }
        // if this post has been posted in a group then check if the user has permession to delete this post
        if ($post->group_id != null) {
            // check if this user is the owner of the group
            if ($post->group->owner->user_id == $user->user_id) {
                $this->delete_post($post_id);
                return response()->json(
                    [
                        'message' => 'the post has been deleted successflly'
                    ],
                    200
                );
            }
            // check if this user is an Admin in this group and the post's owner is not 
            if (
                GroupUser::where([
                    ['group_id', '=', $post->group_id],
                    ['role_id', '=', 1], // Admin role's id is 1
                    ['user_id', '=', $user->user_id]
                ])->exists() &&
                !GroupUser::where([
                    ['group_id', '=', $post->group_id],
                    ['role_id', '=', 1], // Admin role's id is 1
                    ['user_id', '=', $post->user_id]
                ])->exists() &&
                $post->group->owner->user_id != $post->user_id // if the post's owner is the owner of the group then the post will no be deleted

            ) {
                $this->delete_post($post_id);
                return response()->json(
                    [
                        'message' => 'the post has been deleted successflly'
                    ],
                    200
                );
            }
        }
        // check if this post has a page id and this user has permessions to delete this post
        if ($post->page_id) {
            //check if this user is the owner of this page
            if ($post->page->owner->user_id == $user->user_id) {
                $this->delete_post($post_id);
                return response()->json(
                    [
                        'message' => 'the post has been deleted successflly'
                    ],
                    200
                );
            }
            // even if this post has been posted on a public page and this user is an admin in that page
            // he can not delete the post becouse an admin can not delete a post has been posted by another admin
        }
        // this user does not have permessions to delete this post
        return response()->json(
            [
                'message' => 'you do not hane permessions to delete this post'
            ],
            403
        );
    }
}
