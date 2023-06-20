<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BlogRepository;

class BlogController extends Controller
{

    protected $blog;

    public function __construct(Request $request)
    {
        $this->blog = new BlogRepository($request);
    }

    public function index(Request $request)
    {
        $result = $this->blog->index();
        if ($result['status'] == 200) {
            return $this->sendResponse($result['data'], 'Blog retrieved successfully.');
        } else if ($result['status'] == 400) {
            return $this->sendResponse($result['data'], 'Validation Error', $result['status']);
        }
    }

    public function store(Request $request)
    {
        $result = $this->blog->store();
        if ($result['status'] == 201) {
            return $this->sendResponse($result, 'Blog added successfully.', 201);
        } else if ($result['status'] == 400) {
            return $this->sendResponse($result['data'], 'Validation Error', $result['status']);
        }
    }

    public function show($id)
    {
        $result = $this->blog->show($id);
        if ($result['status'] == 200) {
            return $this->sendResponse($result['data'], 'Blog retrieved successfully.');
        } else if ($result['status'] == 404) {
            return $this->sendError('Blog is not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $result = $this->blog->update($id);
        if ($result['status'] == 200) {
            return $this->sendResponse([], 'Blog updated successfully.', 200);
        } else if ($result['status'] == 400) {
            return $this->sendResponse($result['data'], 'Validation Error', $result['status']);
        } else if ($result['status'] == 403) {
            return $this->sendError('Forbidden', 'Access restricted', 403);
        } else {
            return $this->sendError('Blog is not found.');
        }
    }

    public function destroy($id)
    {
        $result = $this->blog->destroy($id);
        if ($result == 200) {
            return $this->sendResponse([], 'Blog deleted successfully');
        } else if ($result == 403) {
            return $this->sendError('Forbidden', 'Access restricted', 403);
        } else {
            return $this->sendError('Blog is not found');
        }
    }

    public function show_comments($id)
    {
        $result = $this->blog->show_comments($id);
        if ($result['status'] == 200) {
            return $this->sendResponse($result['data'], 'Comment retrieved successfully.');
        } else {
            return $this->sendError('Blog is not found');
        }
    }

    public function like_dislike(Request $request, $id)
    {
        $result = $this->blog->like_dislike($id);
        if ($result['status'] == 200 || $result['status'] == 201) {
            return $this->sendResponse([], $result['data']);
        } else if ($result['status'] == 403) {
            return $this->sendError('Forbidden', $result['data'], 403);
        } else if ($result['status'] == 400) {
            return $this->sendResponse($result['data'], 'Validation Error', 400);
        } else {
            return $this->sendError('Blog is not found');
        }
    }
}
