<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <!-- Form Pencarian -->
            <form action="{{ route('folder.show', $folder->id) }}" method="GET" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search files..." class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md">Search</button>
            </form>

            @if(auth()->user()->hasRole('admin'))
            <div class="ml-2 flex space-x-2">
                <form action="{{ route('subfile.store', $folder->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="file-upload" class="inline-flex items-center px-4 py-2 bg-white border border-black rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-200 active:bg-black active:text-white focus:outline-none focus:ring ring-black transition ease-in-out duration-150 cursor-pointer">
                        + Upload
                    </label>
                    <input id="file-upload" type="file" name="file" class="hidden" onchange="this.form.submit()">
                </form>
            </div>
            @endif
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Tampilkan Mode List -->
                <div id="listView" class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-800">
                        <thead class="text-xs text-gray-900 uppercase bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Preview</th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('folder.show', ['id' => $folder->id, 'sort_by' => 'name', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Name
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('folder.show', ['id' => $folder->id, 'sort_by' => 'uploader', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Uploader
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('folder.show', ['id' => $folder->id, 'sort_by' => 'created_at', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Upload Date
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">Size</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr>
                                <td class="px-6 py-4">
                                    @if(in_array(pathinfo($file->name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ Storage::url($file->path) }}" alt="{{ $file->name }}" class="h-12 w-12 object-cover">
                                    @else
                                        <i class="fa fa-file" style="font-size:24px;"></i>
                                    @endif
                                </td>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $file->name }}</td>
                                <td class="px-6 py-4">{{ $file->uploader }}</td>
                                <td class="px-6 py-4">{{ $file->created_at->format('d-m-Y') }}</td>
                                <td class="px-6 py-4">{{ round($file->size / 1024, 2) }} KB</td>
                                <td class="px-4 py-4">
                                    <!-- File Actions -->
                                    <div class="flex space-x-2">
                                        <!-- Download -->
                                        <a href="{{ route('file.download', $file->id) }}" class="px-4 py-2 text-blue-600 hover:bg-gray-100">
                                            <i class="fa fa-download" style="font-size:20px;"></i>
                                        </a>

                                        @if(auth()->user()->hasRole('admin'))
                                        <!--Update -->
                                        <form action="{{ route('file.update', $file->id) }}" method="POST" enctype="multipart/form-data" id="update-form-{{ $file->id }}">
                                            @csrf
                                            <input type="file" name="file" class="hidden" id="file-input-{{ $file->id }}" onchange="document.getElementById('update-form-{{ $file->id }}').submit();" onsubmit="return handleFormSubmit(event, this);">
                                            <button type="button" onclick="document.getElementById('file-input-{{ $file->id }}').click();" class="px-4 py-2 text-yellow-600 hover:bg-gray-100">
                                                <i class="fa fa-upload" style="font-size:20px;"></i>
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <button type="button" onclick="showDeleteModal({{ $file->id}})" class="px-4 py-2 text-red-600 hover:bg-gray-100">
                                            <i class="fa fa-trash" style="font-size:20px;"></i>
                                        </button>

                                        @endif
                                    </div>
                                </td>

                            <!-- Delete File Konfirmasi Modal -->
                                <div id="deleteFileModal-{{$file->id}}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                    <div class="bg-white p-6 rounded-lg shadow-lg">
                                        <h2 class="flex-xl font-semibold mb-4">Delete File</h2>
                                        <p>Are you sure you want to delete this file?</p>
                                        <div class="flex justify-end mt-4">
                                            <button type="button" onclick="hideDeleteModal({{ $file->id }})" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                                            <form action="{{ route('file.destroy', $file->id) }}" method="POST" onsubmit="return handleFormSubmit(event, this);">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md ml-2" >Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-3 bg-white border-gray-200">
                    {{ $files->appends(request()->except('page'))->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const listView = document.getElementById("listView");

            // Default to list view
            listView.classList.remove("hidden");
        });
    </script>

    <!-- Hapus File Modal -->
    <script>
        function showDeleteModal(id) {
            const modal = document.getElementById(`deleteFileModal-${id}`);
            modal.classList.remove("hidden");
        }

        function hideDeleteModal(id) {
            const modal = document.getElementById(`deleteFileModal-${id}`);
            modal.classList.add("hidden");
        }
    </script>

    <!-- Handle Form Submit -->
    <script>
        function handleFormSubmit(event, form) {
            event.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('An error occurred. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            return false;
        }
    </script>
</x-app-layout>
