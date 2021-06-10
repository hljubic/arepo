<?php

namespace App\Http\Controllers;

use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ResourceFileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'file' => 'required|file',
            'attributes' => 'nullable|json',
            'folder' => 'nullable|string',
            'disk' => 'string|in:local,public'
        ]);

        $disk = $request->get('disk') ?? 'public';
        $folder = $request->get('folder') ?? '/';
        $file = $request->file('file');
        $name = $request->get('name') ? $request->get('name'). '.' .$file->getClientOriginalExtension() : $file->getClientOriginalName();

        $filename = md5($file) . now()->timestamp . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk($disk)->putFileAs($folder, $file, $filename);
        // TODO: Folder structure ?

        $uploaded = ResourceFile::create([
            'name' => $name,
            'filepath' => $path,
            'mimetype' => $file->getMimeType(),
            'attributes' => $request->get('attributes')
        ]);

        return $uploaded;
    }

    public function showByUuid(Request $request, string $uuid)
    {
        $resourceFile = ResourceFile::where('uuid', $uuid)->first();
        return Storage::response($resourceFile->filepath, $resourceFile->name);
    }

    public function destroy(Request $request, ResourceFile $resourceFile)
    {
        Storage::delete($resourceFile->filepath);
        $resourceFile->delete();

        return response()->json(['message' => 'Success'], 204);
    }
}
