<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Models\MediaFolder;
use OptimusCMS\Media\Http\Resources\FolderResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OptimusCMS\Media\Http\Requests\StoreFolderRequest;
use OptimusCMS\Media\Http\Requests\UpdateFolderRequest;

class FoldersController extends Controller
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

        return FolderResource::collection($folders);
    }

    /**
     * Create a new folder.
     *
     * @param StoreFolderRequest $request
     * @return FolderResource
     */
    public function store(StoreFolderRequest $request)
    {
        $folder = MediaFolder::create($request->all());

        return new FolderResource($folder);
    }

    /**
     * Display the specified folder.
     *
     * @param int $id
     * @return FolderResource
     */
    public function show($id)
    {
        $folder = MediaFolder::findOrFail($id);

        return new FolderResource($folder);
    }

    /**
     * Update the specified field.
     *
     * @param UpdateFolderRequest $request
     * @param int $id
     * @return FolderResource
     */
    public function update(UpdateFolderRequest $request, $id)
    {
        $folder = MediaFolder::findOrFail($id);

        $folder->update($request->all());

        return new FolderResource($folder);
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

        $folder->media->each->delete();

        $folder->delete();

        return response()->noContent();
    }
}
