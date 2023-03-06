<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function get_all_tags(Request $request)
    {
        return response()->json(
            [
                'message' => 'success',
                'tags' => Tag::all()
            ],
            200
        );
    }
    public function get_tag(Request $request, $tag_id)
    {
        // check if this tag exists
        if (!Tag::where('tag_id', '=', $tag_id)->exists()) {
            return response()->json(
                [
                    'message' => 'no such tag'
                ],
                400
            );
        }
        return response()->json(
            [
                'message' => 'ok',
                'tag' => Tag::find($tag_id)
            ],
            200
        );
    }
}
