<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\BlogLike;
use Illuminate\Support\Facades\Auth;

class BlogRepository
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ? $request : new Request;
    }

    public function index()
    {
        $input = $this->request->all();
        $validator = Validator::make(
            $input,
            [
                'user_id' => ['nullable', 'exists:users,id'],
                'status' => ['nullable'],
                'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            ]
        );

        if ($validator->fails()) {
            return ['status' => 400, 'data' => $validator->errors()];
        }

        $result = Blog::select();
        if ($this->request->user_id) {
            $result = $result->where('user_id', $this->request->user_id);
        }
        if (!is_null($this->request->status)) {
            $result = $result->where('status', $this->request->status);
        }

        if ($this->request->date) {
            $mindate = $this->request->date . ' 00:00:00';
            $maxdate = $this->request->date . ' 23:59:59';
            $result = $result->whereDate('created_at', '>=', $mindate)
                ->whereDate('created_at', '<=', $maxdate);
        }

        $result = $result->orderBy('published_date', 'desc')->paginate(10);
        return ['status' => 200, 'data' => $result];
    }

    public function store()
    {
        $input = $this->request->all();
        $validator = Validator::make(
            $input,
            [
                'title' => ['required', 'min:3', 'max:100'],
                'body' => ['required', 'min:10', 'max:10000'],
            ]
        );
        if ($validator->fails()) {
            return ['status' => 400, 'data' => $validator->errors()];
        }
        $user_id = Auth::user()->id;
        $result = Blog::create(
            array_merge($input, [
                'status' => 1,
                "user_id" => $user_id,
                'published_date' => date('Y-m-d H:i:s')
            ])
        );
        return ['status' => 201, 'data' => $result];
    }

    public function show($id)
    {
        $result = Blog::find($id);
        if (is_null($result)) {
            return ['status' => 404, 'data' => NULL];
        }
        return ['status' => 200, 'data' => $result];
    }

    public function update($id)
    {
        $result = Blog::find($id);
        if (is_null($result)) {
            return ['status' => 404, 'data' => NULL];
        }

        $input = $this->request->all();
        $validator = Validator::make(
            $input,
            [
                'title' => 'required',
                'body' => ['required', 'max:10000'],
                // 'status' => 'required',
                // 'published_date' => 'required',
            ]
        );

        if ($validator->fails()) {
            return ['status' => 400, 'data' => $validator->errors()];
        }

        if (Auth::id() === $result->user_id) {
            $result->update($input);
            return ['status' => 200, 'data' => $result];
            // return $this->sendResponse($blog, 'Blog updated successfully.', 201);
        }

        // return $this->sendError('Forbidden', 'Access restricted', 403);
        return ['status' => 403, 'data' => NULL];
    }

    public function destroy($id)
    {
        $result = Blog::find($id);
        if (is_null($result)) {
            return 404;
        } else {
            if (Auth::id() === $result->user_id) {
                $result->delete();
                return 200;
            }
            return 403;
        }
    }

    public function show_comments($id)
    {
        $result = Blog::find($id);
        if (is_null($result)) {
            return ['status' => 404, 'data' => NULL];
        }
        $result = ($result->comments()->paginate(10));
        return ['status' => 200, 'data' => $result];
    }

    public function like_dislike($id)
    {
        $blog = Blog::find($id);
        if (is_null($blog)) {
            return ['status' => 404, 'data' => NULL];
        }

        $input = $this->request->all();
        $validator = Validator::make(
            $input,
            [
                'status' => 'required',
            ]
        );

        if ($validator->fails()) {
            return ['status' => 400, 'data' => $validator->errors()];
        }

        // check if the user has liked
        $user_id = Auth::user()->id;
        $row = BlogLike::where('user_id', $user_id)->where('blog_id', $id)->first();
        $msg = $this->request->status == 1 ? 'liked' : 'disliked';

        // user never like this blog before
        if (!$row) {
            $create = BlogLike::create(array_merge($input, [
                "blog_id" => $id,
                "user_id" => $user_id,
            ]));
            return ['status' => 201, 'data' => $msg,];
        } else {
            // user has like this blog before
            if ($row->status == $this->request->status) {
                return ['status' => 403, 'data' => $msg];
            } else {
                $update = $row->update(array_merge($input, [
                    "blog_id" => $id,
                    "user_id" => $user_id,
                ]));
                return ['status' => 200, 'data' => $msg];
            }
        }
    }
}
