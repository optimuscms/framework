<?php

namespace OptimusCMS\Posts\Http\Controllers;

use OptimusCMS\Posts\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OptimusCMS\Posts\Http\Resources\Post as PostResource;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::withDrafts()->filter($request)->get();

        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $this->validatePost($request);

        $post = Post::create($request->all());

        if ($request->filled('image')) {
            $post->attachMedia(
                $request->input('image'), 'image', config('post.imageConversions')
            );
        }

        $post->tags()->attach($request->input('tags'));

        return new PostResource($post);
    }

    public function show($id)
    {
        $post = Post::withDrafts()->findOrFail($id);

        return new PostResource($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::withDrafts()->findOrFail($id);

        $this->validatePost($request);

        $post->update($request->all());

        $post->detachMedia();

        if ($request->filled('image')) {
            $post->attachMedia(
                $request->input('image'), 'image', config('post.imageConversions')
            );
        }

        $post->tags()->sync($request->input('tags'));

        return new PostResource($post);
    }

    public function destroy($id)
    {
        Post::withDrafts()->findOrFail($id)->delete();

        return response(null, 204);
    }

    protected function validatePost(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'exists:media,id|nullable',
            'tags' => 'required|array|min:1',
            'tags.*' => 'required|exists:post_tags,id',
            'published_at' => 'date|nullable',
        ]);
    }
}
