<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Optix\Media\MediaUploader;
use OptimusCMS\Media\Models\Media;
use Illuminate\Routing\Controller;
use Optix\Media\Jobs\PerformConversions;
use OptimusCMS\Media\Http\Resources\MediaResource;
use OptimusCMS\Media\Http\Requests\StoreMediaRequest;
use OptimusCMS\Media\Http\Requests\UpdateMediaRequest;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::applyFilters($request->all())->get();

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request)
    {
        $media = MediaUploader::fromFile($request->file('file'))
            ->withAttributes($request->only([
                'folder_id',
                'caption',
                'alt_text',
            ]))
            ->upload();

        if (Str::startsWith($media->mime_type, 'image')) {
            PerformConversions::dispatch($media, [
                'media-thumbnail',
            ]);
        }

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $media = Media::findOrFail($id);

        return new MediaResource($media);
    }

    public function update(UpdateMediaRequest $request, $id)
    {
        $media = Media::findOrFail($id);

        $media->update($request->only([
            'folder_id',
            'caption',
            'alt_text',
            'name',
        ]));

        return new MediaResource($media);
    }

    public function destroy($id)
    {
        Media::findOrFail($id)->delete();

        return response(null, 204);
    }
}
