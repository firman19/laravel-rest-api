<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\CommentLike;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function index()
    {
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make(
            $input,
            [
                'comment' => ['required', 'min:10', 'max:200'],
                'blog_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 400);
        }

        $blog = Blog::find($request->blog_id);
        if (is_null($blog)) {
            return $this->sendError('Blog is not found.');
        }

        $user_id = Auth::user()->id;
        $comment = Comment::create(array_merge($input, [
            "user_id" => $user_id,
        ]));
        return $this->sendResponse($comment, 'Comment added successfully.', 201);
    }

    public function show($id)
    {
        $comment = Comment::find($id);
        if (is_null($comment)) {
            return $this->sendError('Comment is not found.');
        }
        return $this->sendResponse($comment, 'Comment retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (is_null($comment)) {
            return $this->sendError('Comment is not found.');
        }
        $input = $request->all();
        $validator = Validator::make(
            $input,
            [
                'comment' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 400);
        }

        if (Auth::id() === $comment->user_id) {
            $comment->update($input);
            return $this->sendResponse($comment, 'Comment updated successfully.', 201);
        }
        return $this->sendError('Forbidden', 'Access restricted', 403);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);
        if (is_null($comment)) {
            return $this->sendError('Comment is not found');
        } else {
            if (Auth::id() === $comment->user_id) {
                $comment->delete();
                return $this->sendResponse([], 'Comment deleted successfully');
            }
            return $this->sendError('Forbidden', 'Access restricted', 403);
        }
    }

    public function like_dislike(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (is_null($comment)) {
            return $this->sendError('Comment is not found.');
        }

        $input = $request->all();
        $validator = Validator::make(
            $input,
            [
                'status' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 400);
        }

        // check if the user has liked
        $user_id = Auth::user()->id;
        $row = CommentLike::where('user_id', $user_id)->where('comment_id', $id)->first();
        $msg = $request->status == 1 ? 'liked' : 'disliked';

        // user never like this comment before
        if (!$row) {
            $create = CommentLike::create(array_merge(
                $input,
                [
                    "comment_id" => $id,
                    "user_id" => $user_id
                ]
            ));
            return $this->sendResponse([], 'Comment ' . $msg . ' successfully.', 201);
        } else {
            // user has like this comment before
            if ($row->status == $request->status) {
                return $this->sendError('Forbidden', 'You have ' . $msg . ' this comment.', 403);
            } else {
                $update = $row->update(array_merge($input, [
                    "comment_id" => $id,
                    "user_id" => $user_id
                ]));
                return $this->sendResponse($update, 'Comment ' . $msg . ' successfully.', 200);
            }
        }
    }
}
