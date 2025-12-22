@extends('layouts.manager')

@section('title','Hak Akses')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Hak Akses</h1>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Users</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $stats['total_users'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Roles</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $stats['total_roles'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-id-badge text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Managers</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $stats['manager_count'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tie text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Cashiers</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $stats['cashier_count'] ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Users Section -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-base font-semibold text-slate-800">Users</h3>
                <button type="button" onclick="openModal('addUserModal')" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add User
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover">
                                    @else
                                        <div class="w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="font-medium text-slate-700 text-sm">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    {{ $user->role->display_name ?? ucfirst($user->role->name) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openModal('editUserModal-{{ $user->id }}')" class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <form id="deleteUserForm-{{ $user->id }}" action="{{ route('manager.users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-700" onclick="confirmDelete({{ $user->id }})">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Roles Section -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-base font-semibold text-slate-800">Roles</h3>
                <button type="button" onclick="openModal('addRoleModal')" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Role
                </button>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($roles as $role)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-blue-200 transition-colors">
                        <div>
                            <h4 class="font-semibold text-slate-800">{{ $role->display_name ?? ucfirst($role->name) }}</h4>
                            @if($role->description)
                            <p class="text-xs text-slate-600 mt-1">{{ $role->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openModal('editRoleModal-{{ $role->id }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            @if(!in_array($role->name, ['manager', 'kasir']))
                            <form id="deleteRoleForm-{{ $role->id }}" action="{{ route('manager.roles.destroy', $role) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-700" onclick="confirmDelete({{ $role->id }}, 'role')">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Add User</h3>
                    <button type="button" onclick="closeModal('addUserModal')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                            <input type="text" name="name" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                            <input type="password" name="password" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                            <select name="role_id" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name ?? ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addUserModal')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
    <div id="editUserModal-{{ $user->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Edit User - {{ $user->name }}</h3>
                    <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Avatar</label>
                            @if($user->avatar)
                            <div class="mb-2">
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-lg object-cover">
                            </div>
                            @endif
                            <input type="file" name="avatar" accept="image/*" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Password (leave blank to keep)</label>
                            <input type="password" name="password" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                            <select name="role_id" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @if($user->role && $user->role->id == $role->id) selected @endif>{{ $role->display_name ?? ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Add Role</h3>
                    <button type="button" onclick="closeModal('addRoleModal')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.roles.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role Name</label>
                            <input type="text" name="name" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Display Name</label>
                            <input type="text" name="display_name" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addRoleModal')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modals -->
    @foreach($roles as $role)
    <div id="editRoleModal-{{ $role->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Edit Role - {{ $role->display_name ?? $role->name }}</h3>
                    <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role Name</label>
                            <input type="text" name="name" value="{{ $role->name }}" class="w-full border border-slate-300 rounded-lg px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Display Name</label>
                            <input type="text" name="display_name" value="{{ $role->display_name }}" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2">{{ $role->description }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function confirmDelete(id, type = 'user') {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        if (type === 'user') {
            document.getElementById('deleteUserForm-' + id).submit();
        } else {
            document.getElementById('deleteRoleForm-' + id).submit();
        }
    }
}
</script>
@endsection
