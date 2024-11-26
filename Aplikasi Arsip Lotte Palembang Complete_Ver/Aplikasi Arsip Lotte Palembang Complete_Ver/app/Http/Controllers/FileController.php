<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class FileController extends Controller
{
    public function index()
    {
        $sort_by = request('sort_by', 'created_at');  // Default sorting by 'created_at'
        $order = request('order', 'desc');            // Default order is 'desc'
        $search = request('search');                  // Search input

        // Query for folders
        $foldersQuery = Folder::query();
        if ($search) {
            $foldersQuery->where('name', 'like', '%' . $search . '%');
        }
        $folders = $foldersQuery->orderBy($sort_by, $order)->get();

        // Query for files, filtering out those with folder_id not null
        $filesQuery = File::query()->whereNull('folder_id');
        if ($search) {
            $filesQuery->where('name', 'like', '%' . $search . '%');
        }
        $files = $filesQuery->orderBy($sort_by, $order)->get();

        // Ensure both are collections
        $foldersCollection = collect($folders)->map(function ($folder) {
            return ['type' => 'folder', 'data' => $folder];
        });

        $filesCollection = collect($files)->map(function ($file) {
            return ['type' => 'file', 'data' => $file];
        });

        // Combine folders and files into one collection
        $items = $foldersCollection->merge($filesCollection);

        // Paginate the combined collection
        $currentPage = Paginator::resolveCurrentPage();
        $perPage = 10; // Number of items per page
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $items->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);

        return view('file', compact('paginatedItems', 'sort_by', 'order', 'search'));
    }


    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }

        $request->validate([
            'file' => 'required|file|max:102400', // max 100MB
        ]);

        $file = $request->file('file');

        // Menyimpan file ke dalam folder public/uploads
        $path = $file->store('uploads', 'public');

        $uploadedFile = File::create([
            'name' => $file->getClientOriginalName(),
            'uploader' => auth()->user()->name, // Menyimpan nama pengguna yang login
            'path' => $path,
            'size' => $file->getSize(),
        ]);

        return redirect()->route('file')->with('success', 'File uploaded successfully');
    }

    public function download(File $file)
    {
        // Pastikan file ada di disk 'public'
        Storage::disk('public')->exists($file->path);
        // Unduh file dari lokasi yang sesuai di disk 'public'
        return Storage::disk('public')->download($file->path, $file->name);

    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }

        $request->validate([
            'file' => 'required|file|max:102400', // max 100MB
        ]);

        $file = File::findOrFail($id);

        // Hapus file lama
        Storage::disk('public')->delete($file->path);

        // Simpan file baru
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads', 'public');

        // Update informasi file
        $file->path = $path;
        $file->name = $uploadedFile->getClientOriginalName();
        $file->size = $uploadedFile->getSize();
        $file->save();

        return redirect()->route('file')->with('success', 'File updated successfully');
    }

    public function destroy(File $file)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('file')->with('error', 'You do not have permission to perform this action.');
        }
    
        Storage::disk('public')->delete($file->path);
        $file->delete();
    
        return redirect()->route('file')->with('success', 'File deleted successfully');
    }
}

