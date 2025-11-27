@extends('layouts.manager')

@section('title','Add Role')

@section('content')
    <div class="p-8">
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <a href="{{ route('manager.access.index') }}" class="text-blue-600 hover:underline mr-2">← Back</a>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800">Add New Role</h2>
                    <p class="text-gray-600">Create a new role</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                    <form method="POST" action="{{ route('manager.roles.store') }}">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                placeholder="e.g., supervisor, admin">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Use lowercase, no spaces (use underscore instead)</p>
                        </div>

                        <!-- Display Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="display_name">
                                Display Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror"
                                placeholder="e.g., Supervisor, Administrator">
                            @error('display_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Brief description of this role's responsibilities">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-bold">
                                Create Role
                            </button>
                            <a href="{{ route('manager.access.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
