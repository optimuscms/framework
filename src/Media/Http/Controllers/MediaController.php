<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Models\Media;
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
        $mediaItems = Media::with('folder')
            ->applyFilters($request->all())
            ->get();

        return MediaResource::collection($mediaItems);
    }

    /**
     * Upload a file and create a new media item.
     *
     * @param Request $request
     * @return MediaResource
     */
    public function store(Request $request)
    {
        // Todo: Upload media via the MediaUploader class...

        $mediaItem = null;

        return new MediaResource($mediaItem);
    }

    /**
     * Display the specified media item.
     *
     * @param int $id
     * @return MediaResource
     */
    public function show($id)
    {
        $mediaItem = Media::findOrFail($id);

        return new MediaResource($mediaItem);
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
        $mediaItem = Media::findOrFail($id);

        //

        return new MediaResource($mediaItem);
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
    protected function validateMediaItem(Request $request, Media $media = null)
    {
        $request->validate([
            //
        ]);
    }
}
