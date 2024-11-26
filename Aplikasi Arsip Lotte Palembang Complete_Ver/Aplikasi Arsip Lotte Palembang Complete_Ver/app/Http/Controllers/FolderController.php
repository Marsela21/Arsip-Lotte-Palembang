<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\File;

class FolderController extends Controller
{
    public function show(Request $request, $id)
    {
        $sortBy = $request->get('sort_by', 'name');
        $order = $request->get('order', 'asc');
        $search = $request->get('search');

        // Ambil folder berdasarkan ID
        $folder = Folder::findOrFail($id);

        // Ambil file yang terkait dengan folder tersebut
        $query = File::where('folder_id', $id);

        // Cek apakah ada query pencarian
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Terapkan pengurutan dan paginasi
        $files = $query->orderBy($sortBy, $order)->paginate(10);

        // Kirim data ke view
        return view('folder', compact('folder', 'files'));
    }

    public function store(Request $request)
    {
        // // Nama default folder
        // $folderName = 'Folder';

        // // Cek jika nama folder sudah ada di database
        // $existingFolder = Folder::where('name', $folderName)->first();

        // if ($existingFolder) {
        //     return redirect()->route('file')->with('error', 'Folder already exists!');
        // }

        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }

        // Buat folder baru
        $folder = new Folder();
        $folder->name = $request->input('folder_name');
        $folder->uploader = auth()->user()->name;
        $folder->size = 0;
        $folder->save();

        // Redirect ke halaman yang sesuai
        return redirect()->route('file')->with('success', 'Folder created successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }

        // Ambil folder berdasarkan ID
        $folder = Folder::findOrFail($id);

        // Update informasi folder
        $folder->name = $request->input('folder_name');
        $folder->save();

        // Redirect ke halaman yang sesuai
        return redirect()->route('file')->with('success', 'Folder updated successfully!');
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }

        // Temukan folder berdasarkan ID
        $folder = Folder::findOrFail($id);

        // Hapus semua file dalam folder (opsional, jika Anda ingin menghapus file juga)
        foreach ($folder->files as $file) {
            // Hapus file dari storage
            \Storage::delete($file->path);

            // Hapus file dari database
            $file->delete();
        }

        // Hapus folder
        $folder->delete();

        return redirect()->route('file')->with('success', 'Folder deleted successfully!');
    }
}
