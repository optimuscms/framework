<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Models\MediaFolder;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OptimusCMS\Media\Http\Resources\MediaFolderResource;

class MediaFoldersController extends Controller
{
    /**
     * Display all of the media folders.
     *
     * @return ResourceCollection
     */
    public function index()
    {
        $folders = MediaFolder::all();

        return MediaFolderResource::collection($folders);
    }

    /**
     * Create a new media folder.
     *
     * @param Request $request
     * @return MediaFolderResource
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validateFolder($request);

        $folder = new MediaFolder();

        $folder->name = $request->input('name');
        $folder->parent_id = $request->input('parent_id');

        $folder->save();

        return new MediaFolderResource($folder);
    }

    /**
     * Display the specified media folder.
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
     * Update the specified media folder.
     *
     * @param Request $request
     * @param int $id
     * @return MediaFolderResource
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $folder = MediaFolder::findOrFail($id);

        $this->validateFolder($request, $folder);

        $folder->fill($request->only([
            'name',
            'parent_id',
        ]));

        $folder->save();

        return new MediaFolderResource($folder);
    }

    /**
     * Delete the specified media folder.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        MediaFolder::findOrFail($id)->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param Request $request
     * @param MediaFolder|null $folder
     *
     * @throws ValidationException
     */
    protected function validateFolder(Request $request, MediaFolder $folder = null)
    {
        $request->validate([
            'name' => ($folder ? 'filled' : 'required').'|string|max:255',
            // Todo: Must not be an ancestor of the current folder...
            'parent_id' => 'nullable|exists:media_folders,id',
        ]);
    }
}
