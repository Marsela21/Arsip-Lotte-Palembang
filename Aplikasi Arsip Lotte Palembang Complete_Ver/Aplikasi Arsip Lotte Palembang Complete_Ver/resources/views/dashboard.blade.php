<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lotte Drive') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="relative overflow-x-auto">
                    <h1 scope="col" class="px-6 py-3">
                        Selamat datang {{auth()->user()->name}} di aplikasi Arsip Lotte Palembang 
                    </h1>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
