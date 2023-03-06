<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Validator;

class FriendController extends Controller
{
    /*
    status:
    first and second bit for block.
    the first bit for first user
    the second bit for second user
    third bit for waiting request friend from first user to the second user
    */

    public function send_request_friend(Request $request)
    {
        //validation
        $validattion = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,user_id|'
        ]);
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }

        // are they friend or block
        $user = auth()->user();
        $friend_relation = Friend::where(
            ['first_user_id', '=', $request->friend_id],
            ['second_user_id', '=', $user->user_id],
        )->first();

        if (is_null($friend_relation)) {
            $friend_relation = Friend::where(
                ['first_user_id', '=', $user->user_id],
                ['second_user_id', '=', $request->friend_id],
            )->first();
        }

        if (is_null($friend_relation)) {
            // send friend request
            // first user send request to the second user
            Friend::create(
                [
                    'first_user_id' => $user->user_id,
                    'second_user_id' => $request->friend_id,
                    'status' => 4
                ]
            );
            return response()->json(
                [
                    'message' => 'the friend request send successfully, wait the accept from the second user',
                ],
                200
            );
        }



        if ($friend_relation->status & 1) {
            return response()->json(
                [
                    'message' => 'you blocked this user'
                ],
                400
            );
        }


        if ($friend_relation->status & 2) {
            return response()->json(
                [
                    'message' => 'this user blocked you'
                ],
                400
            );
        }

        if ($friend_relation->status & 4) {
            return response()->json(
                [
                    'message' => 'you are already send friend reauest'
                ],
                400
            );
        }


        return response()->json(
            [
                'message' => 'you are already add this friend'
            ],
            400
        );

    }


    public function accept_friend(Request $request)
    {
        //validation
        $validattion = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,user_id'
        ]);
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }


        $user = auth()->user();
        $friend_request = Friend::where(
            ['first_user_id', '=', $request->friend_id],
            ['second_user_id', '=', $user->user_id]
        )->first();


        // are they friend or block
        if (is_null($friend_request) || $friend_request->status != 4) {

            if ($friend_request->status != 4)
                $friend_request->delete();

            return response()->json(
                [
                    'message' => 'the request is canceled'
                ],
                400
            );
        }


        //accept request
        $friend_request->status = 0;
        $friend_request->save();
        // need to get friend info
        $friend = $request->friend_id;
        return response()->json(
            [
                'message' => 'friend request accept successfully, now you are friend',
                'friend' => $friend,
            ],
            201
        );
    }

    public function show_my_recived_friend_requests()
    {
        $user = auth()->user();
        $result = Friend::where(['second_user_id', '=', $user->user_id], ['status', '=', 4])->orderBY('updated_at');

        // need to get users information 

        if (is_null($result)) {
            return response()->json(
                [
                    'message' => 'there is not any request to show',
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => 'friend requests return successfully',
                'friend_request' => $result,
            ],
            200
        );
    }

    public function show_my_send_friend_requests()
    {
        $user = auth()->user();
        $result = Friend::where(['first_user_id', '=', $user->user_id], ['status', '=', 4])->orderBY('updated_at');

        // need to get users information 

        if (is_null($result)) {
            return response()->json(
                [
                    'message' => 'there is not any request to show',
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => 'friend requests return successfully',
                'friend_request' => $result,
            ],
            200
        );
    }

    public function show_my_friends()
    {
        $user = auth()->user();
        $result = Friend::where(['second_user_id', '=', $user->user_id], ['status', '=', 0])
            ->orwhere(['first_user_id', '=', $user->user_id], ['status', '=', 0])
            ->orderBY('updated_at');

        // need to get users information 

        if (is_null($result)) {
            return response()->json(
                [
                    'message' => 'there is not any friends to show',
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => 'your friends return successfully',
                'bloked_users' => $result,
            ],
            200
        );
    }



    public function block_user(Request $request)
    {
        //validation
        $validattion = Validator::make($request->all(), [
            'blocked_user_id' => 'required|exists:users,user_id'
        ]);
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }


        $user = auth()->user();
        $friend_relation = Friend::where(
            ['first_user_id', '=', $request->blocked_user_id],
            ['second_user_id', '=', $user->user_id],
        )->first();

        if (!is_null($friend_relation)) {
            $friend_relation->status = $friend_relation->status | 2;
            $friend_relation->save();
        } else {
            $friend_relation = Friend::where(
                ['first_user_id', '=', $user->user_id],
                ['second_user_id', '=', $request->blocked_user_id],
            )->first();

            if (!is_null($friend_relation)) {
                $friend_relation->status = $friend_relation->status | 1;
                $friend_relation->save();

            } else {
                // first user block the second user
                Friend::create(
                    [
                        'first_user_id' => $user->user_id,
                        'second_user_id' => $request->blocked_user_id,
                        'status' => 1
                    ]
                );
            }

        }

        return response()->json(
            [
                'message' => 'you block this user successfully',
            ],
            200
        );

    }




    public function unblock_user(Request $request)
    {
        //validation
        $validattion = Validator::make($request->all(), [
            'unblocked_user_id' => 'required|exists:users,user_id'
        ]);
        if ($validattion->fails()) {
            return response()->json(
                [
                    'message' => $validattion->errors()
                ],
                400
            );
        }


        $user = auth()->user();
        $friend_relation = Friend::where(
            ['first_user_id', '=', $request->unblocked_user_id],
            ['second_user_id', '=', $user->user_id],
        )->first();

        if (!is_null($friend_relation)) {
            $friend_relation->status = $friend_relation->status & (-1 ^ 2);
            $friend_relation->save();
        } else {
            $friend_relation = Friend::where(
                ['first_user_id', '=', $user->user_id],
                ['second_user_id', '=', $request->unblocked_user_id],
            )->first();

            if (!is_null($friend_relation)) {
                $friend_relation->status = $friend_relation->status & (-1 ^ 1);
                $friend_relation->save();

            } else {
                return response()->json(
                    [
                        'message' => 'you are already unblock this user',
                    ],
                    200
                );
            }

        }

        return response()->json(
            [
                'message' => 'you unblock this user successfully',
            ],
            200
        );
    }


    public function my_block_users()
    {
        $user = auth()->user();
        $result = Friend::where(['second_user_id', '=', $user->user_id], ['status', '&', 2])
            ->orwhere(['first_user_id', '=', $user->user_id], ['status', '&', 1])
            ->orderBY('updated_at');

        // need to get users information 

        if (is_null($result)) {
            return response()->json(
                [
                    'message' => 'there is not any blocked user to show',
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => 'blocked users return successfully',
                'bloked_users' => $result,
            ],
            200
        );
    }



}