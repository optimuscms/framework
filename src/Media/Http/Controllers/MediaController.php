<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Optix\Media\MediaUploader;
use Illuminate\Http\JsonResponse;
use OptimusCMS\Media\Models\Media;
use Illuminate\Routing\Controller;
use Optix\Media\Jobs\PerformConversions;
use OptimusCMS\Media\Http\Resources\MediaResource;
use OptimusCMS\Media\Http\Requests\StoreMediaRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OptimusCMS\Media\Http\Requests\UpdateMediaRequest;

class MediaController extends Controller
{
    /**
     * Display a list of media items.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $media = Media::applyFilters($request->all())->get();

        return MediaResource::collection($media);
    }

    /**
     * Create a new media item.
     *
     * @param StoreMediaRequest $request
     * @return JsonResponse
     */
    public function store(StoreMediaRequest $request)
    {
        $media = MediaUploader::fromFile($request->file('file'))
            ->withAttributes($request->only([
                'folder_id',
                'alt_text',
                'caption',
            ]))
            ->upload();

        if (Str::startsWith($media->mime_type, 'image')) {
            PerformConversions::dispatch($media, [
                Media::THUMBNAIL_CONVERSION
            ]);
        }

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    /**
     * Display the specified media item.
     *
     * @param int $id
     * @return MediaResource
     */
    public function show($id)
    {
        $media = Media::findOrFail($id);

        return new MediaResource($media);
    }

    /**
     * Update the specified media item.
     *
     * @param UpdateMediaRequest $request
     * @param int $id
     * @return MediaResource
     */
    public function update(UpdateMediaRequest $request, $id)
    {
        $media = Media::findOrFail($id);

        $media->update($request->only([
            'folder_id',
            'alt_text',
            'caption',
            'name',
        ]));

        return new MediaResource($media);
    }

    /**
     * Delete the specified media item.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        Media::findOrFail($id)->delete();

        return response()->noContent();
    }
}
