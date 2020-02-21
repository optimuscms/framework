<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Http\Resources\MediaFolderResource;
use OptimusCMS\Media\Models\Media;
use OptimusCMS\Media\Models\MediaFolder;

class MediaFoldersController extends Controller
{
    /**
     * Display a list of folders.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $folders = MediaFolder::applyFilters($request->all())->get();

        return MediaFolderResource::collection($folders);
    }

    /**
     * Create a new folder.
     *
     * @param Request $request
     * @return MediaFolderResource
     */
    public function store(Request $request)
    {
        $this->validateFolder($request);

        $folder = MediaFolder::create($request->all());

        return new MediaFolderResource($folder);
    }

    /**
     * Display the specified folder.
     *
     * @param int $id
     * @return MediaFolderResource
     */
    public function show($id)
    {
        $folder = MediaFolder::findOrFail($id);

        return new MediaFolderResource($folder);
    }

    /**
     * Update the specified field.
     *
     * @param Request $request
     * @param int $id
     * @return MediaFolderResource
     */
    public function update(Request $request, $id)
    {
        $folder = MediaFolder::findOrFail($id);

        $this->validateFolder($request, $folder);

        $folder->update($request->all());

        return new MediaFolderResource($folder);
    }

    /**
     * Delete the specified folder.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $folder = MediaFolder::findOrFail($id);

        // Loop through the media in the specified folder
        // and delete them one by one so the model event
        // is handled...
        $folder->media->each(function (Media $media) {
            $media->delete();
        });

        $folder->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param Request $request
     * @param MediaFolder|null $folder
     * @return void
     */
    protected function validateFolder(Request $request, MediaFolder $folder = null)
    {
        $request->validate([
            'name' => ($folder ? 'filled' : 'required').'|string|max:255',
            'parent_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($folder) {
                    $parentFolder = MediaFolder::find($value);

                    // Verify that the parent folder exists and is not
                    // a descendant of the folder being updated...
                    if (! $parentFolder || (
                        $parentFolder
                        && $folder
                        && $parentFolder->isDescendantOf($folder)
                    )) {
                        $fail(__('validation.exists', [
                            'attribute' => 'parent',
                        ]));
                    }
                },
            ],
        ]);
    }
}
