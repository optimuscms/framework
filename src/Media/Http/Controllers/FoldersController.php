<?php

namespace OptimusCMS\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OptimusCMS\Media\Models\MediaFolder;
use OptimusCMS\Media\Http\Resources\FolderResource;
use OptimusCMS\Media\Http\Requests\StoreFolderRequest;
use OptimusCMS\Media\Http\Requests\UpdateFolderRequest;

class FoldersController extends Controller
{
    public function index(Request $request)
    {
        $folders = MediaFolder::applyFilters($request->all())->get();

        return FolderResource::collection($folders);
    }

    public function store(StoreFolderRequest $request)
    {
        $folder = MediaFolder::create($request->all());

        return new FolderResource($folder);
    }

    public function show($id)
    {
        $folder = MediaFolder::findOrFail($id);

        return new FolderResource($folder);
    }

    public function update(UpdateFolderRequest $request, $id)
    {
        $folder = MediaFolder::findOrFail($id);

        $folder->update($request->all());

        return new FolderResource($folder);
    }

    public function destroy($id)
    {
        $folder = MediaFolder::findOrFail($id);

        $folder->media->each->delete();

        $folder->delete();

        return response(null, 204);
    }
}
