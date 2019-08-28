<?php

namespace OptimusCMS\Posts\Http\Controllers;

use OptimusCMS\Posts\PostComment;
use Illuminate\Routing\Controller;
use OptimusCMS\Posts\Http\Resources\Comment as CommentResource;

class CommentsController extends Controller
{
    public function index()
    {
        $comments = PostComment::all();

        return CommentResource::collection($comments);
    }

    public function show($id)
    {
        $comment = PostComment::findOrFail($id);

        return new CommentResource($comment);
    }

    public function destroy($id)
    {
        PostComment::findOrFail($id)->delete();

        return response(null, 204);
    }
}
