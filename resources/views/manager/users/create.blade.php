<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-blue-200">Manager Dashboard</p>
            </div>
            @extends('layouts.manager')

            @section('title','Add User')

            @section('content')
                <div class="p-8">
                    <form method="POST" action="{{ route('manager.users.store') }}">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-bold">
                                Create User
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
