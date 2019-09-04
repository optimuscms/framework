<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Optix\Media\MediaUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Models\Media;
use Optix\Media\Jobs\PerformConversions;
use OptimusCMS\Media\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validateMedia($request);

        $media = MediaUploader::fromFile($request->file('file'))
            ->withAttributes($request->only([
                'folder_id',
                'alt_text',
                'caption',
            ]))
            ->upload();

        // Create a thumbnail for the media manager
        // if the media item is an image...
        if ($media->isOfType('image')) {
            PerformConversions::dispatch($media, [
                Media::THUMBNAIL_CONVERSION,
            ]);
        }

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
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
     * @param Request $request
     * @param int $id
     * @return MediaResource
     */
    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $this->validateMedia($request, $media);

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

    /**
     * Validate the request.
     *
     * @param Request $request
     * @param Media|null $media
     * @return void
     */
    protected function validateMedia(Request $request, Media $media = null)
    {
        if (! $media) {
            // Retrieve the configured maximum file size, default
            // to 5mb if a value has not been specified...
            $maxFileSize = config('media.max_file_size', 5 * 1024);

            $rules = [
                'file' => 'required|file|max:'.$maxFileSize,
            ];
        } else {
            $rules = [
                'name' => 'filled|string|max:255',
                'alt_text' => 'nullable|string|max:255',
                'caption' => 'nullable|string|max:255',
            ];
        }

        $request->validate(array_merge($rules, [
            'folder_id' => 'nullable|exists:media_folders,id',
        ]));
    }
}
