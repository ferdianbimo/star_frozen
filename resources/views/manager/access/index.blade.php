@extends('layouts.manager')

@section('title','Hak Akses')

@section('content')
    <div class="p-6 lg:p-8">
        <!-- Modern Header -->
        <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-8 shadow-xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fas fa-user-shield text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Hak Akses</h2>
                        <p class="text-slate-400 text-sm">Kelola users dan roles sistem</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-slate-700/50 rounded-xl border border-slate-600">
                        <span class="text-slate-400 text-sm"><i class="fas fa-clock mr-2"></i>{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success/error alerts are now shown as toast at top-right --}}

        <!-- Statistics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-8">
            <div class="bg-white p-4 lg:p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Users</p>
                        <p class="text-xl lg:text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-users text-base lg:text-xl text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 lg:p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Roles</p>
                        <p class="text-xl lg:text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_roles'] }}</p>
                    </div>
                    <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-id-badge text-base lg:text-xl text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 lg:p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-slate-500 font-medium">Managers</p>
                        <p class="text-xl lg:text-3xl font-bold text-slate-800 mt-1">{{ $stats['manager_count'] }}</p>
                    </div>
                    <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <i class="fas fa-user-tie text-base lg:text-xl text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 lg:p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-slate-500 font-medium">Cashiers</p>
                        <p class="text-xl lg:text-3xl font-bold text-slate-800 mt-1">{{ $stats['cashier_count'] }}</p>
                    </div>
                    <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="fas fa-cash-register text-base lg:text-xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Users Section -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Users</h3>
                        </div>
                        <button type="button" onclick="openModal('addUserModal')" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i>Add User
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">Name</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">Email</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">Role</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-slate-600 to-slate-700 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-slate-700">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200">
                                        {{ $user->role->display_name ?? ucfirst($user->role->name) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openModal('editUserModal-{{ $user->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                        <form id="deleteUserForm-{{ $user->id }}" action="{{ route('manager.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" onclick="confirmAction('Apakah Anda yakin ingin menghapus user ini?', function() { document.getElementById('deleteUserForm-{{ $user->id }}').submit(); }, { title: 'Hapus User', confirmText: 'Ya, Hapus' })" title="Delete">
                                                <i class="fas fa-trash"></i>
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
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Roles Section -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-id-badge text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Roles</h3>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($roles as $role)
                    <div class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-purple-500/30 flex-shrink-0">
                                    <i class="fas fa-user-tag text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $role->display_name ?? ucfirst($role->name) }}</h4>
                                    <p class="text-sm text-slate-500"><i class="fas fa-users text-xs mr-1"></i>{{ $role->users_count }} users</p>
                                    @if($role->description)
                                    <p class="text-xs text-slate-400 mt-1">{{ $role->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openModal('editRoleModal-{{ $role->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(!in_array($role->name, ['manager', 'kasir']))
                                <form id="deleteRoleForm-{{ $role->id }}" action="{{ route('manager.roles.destroy', $role) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" onclick="confirmAction('Apakah Anda yakin ingin menghapus role ini?', function() { document.getElementById('deleteRoleForm-{{ $role->id }}').submit(); }, { title: 'Hapus Role', confirmText: 'Ya, Hapus' })" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 overflow-y-auto" onclick="if(event.target === this) closeModal('addUserModal')">
        <div class="flex items-center justify-center w-full min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-4 lg:p-6 shadow-2xl my-auto">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="fas fa-user-plus text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Add User</h3>
                    </div>
                    <button type="button" onclick="closeModal('addUserModal')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form" value="create">
                    <div class="space-y-4">
                        @if($errors->any() && old('form') == 'create')
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                                <ul class="list-disc pl-5 text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Avatar Upload -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Foto Profil</label>
                            <div class="flex items-center gap-4">
                                <div id="addUserAvatarPreview" class="w-16 h-16 bg-gradient-to-br from-slate-200 to-slate-300 rounded-xl flex items-center justify-center overflow-hidden">
                                    <i class="fas fa-user text-slate-400 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="avatar" id="addUserAvatar" accept="image/*" class="hidden" onchange="previewAvatar(this, 'addUserAvatarPreview')">
                                    <label for="addUserAvatar" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl cursor-pointer transition-colors">
                                        <i class="fas fa-camera"></i>
                                        <span>Pilih Foto</span>
                                    </label>
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG. Maks 2MB</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                            <input name="name" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input name="email" type="email" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                            <input name="password" type="password" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                            <input name="password_confirmation" type="password" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                            <div class="relative">
                                <select name="role_id" class="custom-select w-full appearance-none border-2 border-slate-200 rounded-xl px-4 py-2.5 pr-10 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer hover:border-slate-300">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name ?? ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addUserModal')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" id="createUserBtn">
                            <i class="fas fa-spinner fa-spin hidden" id="createUserLoading"></i>
                            <span id="createUserText">Create</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
    <div id="editUserModal-{{ $user->id }}" class="modal-overlay hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50" onclick="if(event.target === this) closeModal('editUserModal-{{ $user->id }}')">
        <div class="flex items-center justify-center w-full min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i class="fas fa-user-edit text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Edit User - {{ $user->name }}</h3>
                    </div>
                    <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form" value="edit">
                    <input type="hidden" name="edit_user_id" value="{{ $user->id }}">
                    <div class="space-y-4">
                        @if($errors->any() && old('form') == 'edit' && old('edit_user_id') == $user->id)
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                                <ul class="list-disc pl-5 text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Avatar Upload -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Foto Profil</label>
                            <div class="flex items-center gap-4">
                                <div id="editUserAvatarPreview-{{ $user->id }}" class="w-16 h-16 bg-gradient-to-br from-slate-200 to-slate-300 rounded-xl flex items-center justify-center overflow-hidden">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-slate-600 font-bold text-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="avatar" id="editUserAvatar-{{ $user->id }}" accept="image/*" class="hidden" onchange="previewAvatar(this, 'editUserAvatarPreview-{{ $user->id }}')">
                                    <div class="flex items-center gap-2">
                                        <label for="editUserAvatar-{{ $user->id }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl cursor-pointer transition-colors">
                                            <i class="fas fa-camera"></i>
                                            <span>{{ $user->avatar ? 'Ganti Foto' : 'Pilih Foto' }}</span>
                                        </label>
                                        @if($user->avatar)
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                            <span class="text-sm text-red-600">Hapus foto</span>
                                        </label>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG. Maks 2MB</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                            <input name="name" type="text" value="{{ $user->name }}" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input name="email" type="email" value="{{ $user->email }}" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password (leave blank to keep)</label>
                            <input name="password" type="password" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                            <input name="password_confirmation" type="password" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                            <div class="relative">
                                <select name="role_id" class="custom-select w-full appearance-none border-2 border-slate-200 rounded-xl px-4 py-2.5 pr-10 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer hover:border-slate-300">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @if($user->role && $user->role->id == $role->id) selected @endif>{{ $role->display_name ?? ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-medium hover:shadow-lg hover:shadow-amber-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 edit-user-submit-btn">
                            <i class="fas fa-spinner fa-spin hidden edit-user-loading"></i>
                            <span class="edit-user-text">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Edit Role Modals -->
    @foreach($roles as $role)
    <div id="editRoleModal-{{ $role->id }}" class="modal-overlay hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50" onclick="if(event.target === this) closeModal('editRoleModal-{{ $role->id }}')">
        <div class="flex items-center justify-center w-full min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-user-tag text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Edit Role - {{ $role->display_name ?? $role->name }}</h3>
                    </div>
                    <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('manager.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Name (slug)</label>
                            <input name="name" type="text" value="{{ $role->name }}" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Display Name</label>
                            <input name="display_name" type="text" value="{{ $role->display_name }}" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                            <textarea name="description" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all" rows="3">{{ $role->description }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium hover:shadow-lg hover:shadow-emerald-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 edit-role-submit-btn">
                            <i class="fas fa-spinner fa-spin hidden edit-role-loading"></i>
                            <span class="edit-role-text">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script>
        function openModal(id){
            const el = document.getElementById(id);
            if(!el) return;
            el.classList.remove('hidden');
        }
        function closeModal(id){
            const el = document.getElementById(id);
            if(!el) return;
            el.classList.add('hidden');
        }
        // Close modals with Escape
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape'){
                document.querySelectorAll('.modal-overlay').forEach(m => m.classList.add('hidden'));
            }
        });

        // Avatar preview function
        function previewAvatar(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form submission loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Create user form
            document.querySelector('#addUserModal form').addEventListener('submit', function() {
                const btn = document.getElementById('createUserBtn');
                const loading = document.getElementById('createUserLoading');
                const text = document.getElementById('createUserText');
                btn.disabled = true;
                loading.classList.remove('hidden');
                text.textContent = 'Creating...';
            });

            // Edit user forms
            document.querySelectorAll('.edit-user-submit-btn').forEach(btn => {
                btn.closest('form').addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.querySelector('.edit-user-loading').classList.remove('hidden');
                    btn.querySelector('.edit-user-text').textContent = 'Saving...';
                });
            });

            // Edit role forms
            document.querySelectorAll('.edit-role-submit-btn').forEach(btn => {
                btn.closest('form').addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.querySelector('.edit-role-loading').classList.remove('hidden');
                    btn.querySelector('.edit-role-text').textContent = 'Saving...';
                });
            });
        });
    </script>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const formType = @json(old('form'));
            if(formType === 'create'){
                openModal('addUserModal');
            } else if(formType === 'edit'){
                const id = @json(old('edit_user_id'));
                if(id) openModal('editUserModal-' + id);
            }
        });
    </script>
@endif

@endsection
