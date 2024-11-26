<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">         
            <!-- Form Search Bar -->
            <form method="GET" action="{{ route('file') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md">Search</button>
            </form>

            @if(auth()->user()->hasRole('admin'))
            <div class="ml-2 flex space-x-2">
                <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="file-upload" class="inline-flex items-center px-4 py-2 bg-white border border-black rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-200 active:bg-black active:text-white focus:outline-none focus:ring ring-black transition ease-in-out duration-150 cursor-pointer">
                        + Upload
                    </label>
                    <input id="file-upload" type="file" name="file" class="hidden" onchange="this.form.submit()">
                </form>

            <div class="ml-2 flex space-x-1">
                <!-- Button to open the modal -->
                <button id="createFolderBtn" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-black rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-200 active:bg-black active:text-white focus:outline-none focus:ring ring-black transition ease-in-out duration-150 cursor-pointer">
                    Create Folder
                </button>

                <!-- Modal Pop-up -->
                <div id="createFolderModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold mb-4">Create New Folder</h2>
                        <form action="{{ route('folder.create') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="folder-name" class="block text-sm font-medium text-gray-700">Folder Name</label>
                                <input type="text" name="folder_name" id="folder-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" id="cancelBtn" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-md mr-2">Cancel</button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="relative overflow-x-auto">
                    <!-- Tampilkan Mode List -->
                    <div id="listView" class="hidden">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-800">
                        <thead class="text-xs text-gray-900 uppercase bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Preview</th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('file', ['sort_by' => 'name', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Name
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('file', ['sort_by' => 'uploader', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Uploader
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('file', ['sort_by' => 'created_at', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}">
                                        Upload Date
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3">Size</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($paginatedItems as $item)
                            @if($item['type'] === 'folder')
                                <!-- Display Folder -->
                                <tr>
                                    <!-- Menampilkan icon folder -->
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <i class="fa fa-folder" style="font-size:36px;"></i>
                                    </td>

                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <a href="{{ route('folder.show', $item['data']->id) }}">[Folder] {{ $item['data']->name }}</a>
                                    </td>
                                    <td class="px-6 py-4">{{ $item['data']->uploader ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $item['data']->created_at->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4">-</td>
                                    <td class="px-4 py-4">
                                        <!-- Actions for Folder -->
                                        @if(auth()->user()->hasRole('admin'))
                                        <!-- Folder Actions -->                                     
                                        <div class="flex space-x-2">
                                            <!-- Rename Folder -->
                                            <button type="button" onclick="showRenameModal({{ $item['data']->id }})" class="px-4 py-2 text-yellow-600 hover:bg-gray-100">
                                                <i class="fa fa-edit" style="font-size:20px;"></i>
                                            </button>

                                            <!-- Delete Folder -->
                                            <button type="button" onclick="showDeleteModal({{ $item['data']->id }})" class="px-4 py-2 text-red-600 hover:bg-gray-100">
                                                <i class="fa fa-trash" style="font-size:20px;"></i>
                                            </button>

                                        </div>

                                        <!-- Rename Folder Modal -->
                                        <div id="renameFolderModal-{{ $item['data']->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                                <h2 class="text-xl font-semibold mb-4">Rename Folder</h2>
                                                <form action="{{ route('folder.update', $item['data']->id) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="mb-4">
                                                        <label for="folder-name-{{ $item['data']->id }}" class="block text-sm font-medium text-gray-700">Folder Name</label>
                                                        <input type="text" name="folder_name" id="folder-name-{{ $item['data']->id }}" value="{{ $item['data']->name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                                    </div>
                                                    <div class="flex justify-end">
                                                        <button type="button" onclick="hideRenameModal({{ $item['data']->id }})" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-md mr-2">Cancel</button>
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Delete Folder Konfirmasi Modal -->
                                        <div id="deleteFolderModal-{{ $item['data']->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                                <h2 class="text-xl font-semibold mb-4">Delete Folder</h2>
                                                <p>Are you sure you want to delete this folder?</p>
                                                <div class="flex justify-end mt-4">
                                                    <button type="button" onclick="hideDeleteModal({{ $item['data']->id }})" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-md mr-2">Cancel</button>
                                                    <form action="{{ route('folder.destroy', $item['data']->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    </td>
                                </tr>
                            @elseif($item['type'] === 'file')
                                <!-- Display File -->
                                <tr>
                                    @if ($item['data']->folder_id == null)
                                    <!-- Menampilkan icon preview dari file tersebut seperti gambar untuk jpg, jpeg, dll ataupun file lainny -->
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        @if(in_array(pathinfo($item['data']->name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ Storage::url($item['data']->path) }}" alt="{{ $item['data']->name }}" class="h-12 w-12 object-cover inline-block">
                                        @else
                                            <i class="fa fa-file" style="font-size:36px;"></i>
                                        @endif
                                    </td>
                                    
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $item['data']->name }}</td>
                                    <td class="px-6 py-4">{{ $item['data']->uploader }}</td>
                                    <td class="px-6 py-4">{{ $item['data']->created_at->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4">{{ round($item['data']->size / 1024, 2) }} KB</td>
                                    <td class="px-4 py-4">
                                        <!-- Actions for File -->
                                        <!-- Add your file actions here -->
                                        <div class="flex space-x-2">
                                            <!-- Download File -->
                                            <a href="{{ route('file.download', $item['data']->id) }}" class="px-4 py-2 text-blue-600 hover:bg-gray-100">
                                                <i class="fa fa-download" style="font-size:20px;"></i>
                                            </a>

                                            @if(auth()->user()->hasRole('admin'))
                                            <!-- Update File -->
                                            <form action="{{ route('file.update', $item['data']->id) }}" method="POST" enctype="multipart/form-data" class="hidden" id="update-form-{{ $item['data']->id }}">
                                                @csrf
                                                <input type="file" name="file" id="file-input-{{ $item['data']->id }}" onchange="document.getElementById('update-form-{{ $item['data']->id }}').submit();">
                                            </form>
                                            <button onclick="document.getElementById('file-input-{{ $item['data']->id }}').click();" class="px-4 py-2 text-yellow-600 hover:bg-gray-100">
                                                <i class="fa fa-upload" style="font-size:20px;"></i>
                                            </button>

                                            <!-- Delete File -->
                                            <button type="button" onclick="showDeleteModal({{ $item['data']->id }})" class="px-4 py-2 text-red-600 hover:bg-gray-100">
                                                <i class="fa fa-trash" style="font-size:20px;"></i>
                                            </button>

                                            @endif
                                        </div>
                                    </td>

                                    <!-- Delete File Konfirmasi Modal -->
                                    <div id="deleteFileModal-{{ $item['data']->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                        <div class="bg-white p-6 rounded-lg shadow-lg">
                                            <h2 class="text-xl font-semibold mb-4">Delete File</h2>
                                            <p>Are you sure you want to delete this file?</p>
                                            <div class="flex justify-end mt-4">
                                                <button type="button" onclick="hideDeleteModal({{ $item['data']->id }})" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-md mr-2">Cancel</button>
                                                <form action="{{ route('file.destroy', $item['data']->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif                                   
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    </div>

                    <div class="px-6 py-3 bg-white border-gray-200">
                        {{ $paginatedItems->appends(request()->except('page'))->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Script -->
<script>
    // Menambahkan event listener untuk toggle dropdown saat tombol diklik
    document.querySelectorAll('.dropdownMenuButton').forEach(button => {
        button.addEventListener('click', function() {
            // Tutup semua dropdown yang lain
            document.querySelectorAll('.dropdownMenu').forEach(menu => {
                if (menu !== this.nextElementSibling) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle dropdown terkait
            var dropdown = this.nextElementSibling;
            dropdown.classList.toggle('hidden');
        });
    });

    // Optional: Menutup dropdown jika klik di luar elemen
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdownMenu').forEach(menu => {
            if (!menu.parentElement.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    });
</script>

<!-- Script for handling modal -->
<script>
    document.getElementById('createFolderBtn').addEventListener('click', function() {
        document.getElementById('createFolderModal').classList.remove('hidden');
    });

    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('createFolderModal').classList.add('hidden');
    });
</script>

<script>
    document.getElementById('renameFolderButton').addEventListener('click', function() {
    document.getElementById('renameFolderModal').classList.remove('hidden');
    });

    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('renameFolderModal').classList.add('hidden');
    });
</script>

<script>
    function showRenameModal(id) {
    document.getElementById('renameFolderModal-' + id).classList.remove('hidden');
    }

    function hideRenameModal(id) {
    document.getElementById('renameFolderModal-' + id).classList.add('hidden');
    }
</script>

<!-- Script for handling view toggle (Normal View) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const listView = document.getElementById("listView");

        // Default to normal view
        listView.classList.remove("hidden");
    });
</script>

<!-- Script for handling delete Folder modal -->
<script>
    function showDeleteModal(id) {
        document.getElementById('deleteFolderModal-' + id).classList.remove('hidden');
    }

    function hideDeleteModal(id) {
        document.getElementById('deleteFolderModal-' + id).classList.add('hidden');
    }
</script>

<!-- Script for handling delete File modal -->
<script>
    function showDeleteModal(id) {
        document.getElementById('deleteFileModal-' + id).classList.remove('hidden');
    }

    function hideDeleteModal(id) {
        document.getElementById('deleteFileModal-' + id).classList.add('hidden');
    }
</script>

</x-app-layout>
