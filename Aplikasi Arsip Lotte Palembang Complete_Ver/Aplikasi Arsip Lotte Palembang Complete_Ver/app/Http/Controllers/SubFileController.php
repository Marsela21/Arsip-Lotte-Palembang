<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;

class SubFileController extends Controller
{
    //
    public function store(Request $request, $folderId)
    {
        // Temukan folder berdasarkan ID
        $folder = Folder::findOrFail($folderId);
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('folder.show', $folder->id)->with('error', 'You do not have permission to perform this action.');
        }

        // Validasi file upload
        $request->validate([
            'file' => 'required|file|max:102400',
        ]);

        // Simpan file
        $file = $request->file('file');

        // Menyimpan file ke dalam folder public/uploads
        $path = $file->store('uploads', 'public');

        // Buat entri file di database
        $fileRecord = new File();
        $fileRecord->name = $file->getClientOriginalName();
        $fileRecord->uploader = auth()->user()->name;
        $fileRecord->path = $path;
        $fileRecord->size = $file->getSize();
        $fileRecord->folder_id = $folder->id;
        $fileRecord->save();

        return redirect()->route('folder.show', $folder->id)->with('success', 'File uploaded successfully!');
    }
}
