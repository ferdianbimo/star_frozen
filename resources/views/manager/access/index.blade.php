@extends('layouts.manager')

@section('title','Hak Akses')

@section('content')
    <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Hak Akses</h2>
                    <p class="text-gray-600">Kelola users dan roles</p>
                </div>

                {{-- Success/error alerts are now shown as toast at top-right --}}

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Total Users</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Total Roles</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_roles'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Managers</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['manager_count'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Cashiers</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['cashier_count'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Users Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Users</h3>
                            <button type="button" onclick="openModal('addUserModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                + Add User
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Name</th>
                                        <th class="text-left py-2">Email</th>
                                        <th class="text-left py-2">Role</th>
                                        <th class="text-left py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2">{{ $user->name }}</td>
                                        <td class="py-2">{{ $user->email }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                                {{ $user->role->display_name ?? ucfirst($user->role->name) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <button type="button" onclick="openModal('editUserModal-{{ $user->id }}')" class="text-blue-600 hover:underline text-sm mr-2">Edit</button>
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('manager.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                    <!-- Roles Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Roles</h3>
                            <button type="button" onclick="openModal('addRoleModal')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                + Add Role
                            </button>
                        </div>
                        <div class="space-y-3">
                            @foreach($roles as $role)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-lg">{{ $role->display_name ?? ucfirst($role->name) }}</h4>
                                        <p class="text-sm text-gray-600">{{ $role->users_count }} users</p>
                                        @if($role->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="openModal('editRoleModal-{{ $role->id }}')" class="text-blue-600 hover:underline text-sm">Edit</button>
                                        @if(!in_array($role->name, ['manager', 'kasir']))
                                        <form action="{{ route('manager.roles.destroy', $role) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Are you sure?')">Delete</button>
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
    </div>

    <!-- Modals -->
    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50" onclick="if(event.target === this) closeModal('addUserModal')">
        <div class="flex items-center justify-center w-full min-h-screen">
            <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Add User</h3>
                <button type="button" onclick="closeModal('addUserModal')" class="text-gray-600">&times;</button>
            </div>
            <form action="{{ route('manager.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form" value="create">
                <div class="space-y-3">
                    @if($errors->any() && old('form') == 'create')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm">Name</label>
                        <input name="name" type="text" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Email</label>
                        <input name="email" type="email" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Password</label>
                        <input name="password" type="password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Role</label>
                        <select name="role_id" class="w-full border rounded px-3 py-2">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name ?? ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('addUserModal')" class="px-4 py-2 rounded border">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
    <div id="editUserModal-{{ $user->id }}" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50" onclick="if(event.target === this) closeModal('editUserModal-{{ $user->id }}')">
        <div class="flex items-center justify-center w-full min-h-screen">
            <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit User - {{ $user->name }}</h3>
                <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="text-gray-600">&times;</button>
            </div>
            <form action="{{ route('manager.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form" value="edit">
                <input type="hidden" name="edit_user_id" value="{{ $user->id }}">
                <div class="space-y-3">
                    @if($errors->any() && old('form') == 'edit' && old('edit_user_id') == $user->id)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm">Name</label>
                        <input name="name" type="text" value="{{ $user->name }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Email</label>
                        <input name="email" type="email" value="{{ $user->email }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Password (leave blank to keep)</label>
                        <input name="password" type="password" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm">Role</label>
                        <select name="role_id" class="w-full border rounded px-3 py-2">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($user->role && $user->role->id == $role->id) selected @endif>{{ $role->display_name ?? ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editUserModal-{{ $user->id }}')" class="px-4 py-2 rounded border">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endforeach

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50" onclick="if(event.target === this) closeModal('addRoleModal')">
        <div class="flex items-center justify-center w-full min-h-screen">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Add Role</h3>
                <button type="button" onclick="closeModal('addRoleModal')" class="text-gray-600">&times;</button>
            </div>
            <form action="{{ route('manager.roles.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm">Name (slug)</label>
                        <input name="name" type="text" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Display Name</label>
                        <input name="display_name" type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm">Description</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('addRoleModal')" class="px-4 py-2 rounded border">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit Role Modals -->
    @foreach($roles as $role)
    <div id="editRoleModal-{{ $role->id }}" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 z-50" onclick="if(event.target === this) closeModal('editRoleModal-{{ $role->id }}')">
        <div class="flex items-center justify-center w-full min-h-screen">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit Role - {{ $role->display_name ?? $role->name }}</h3>
                <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="text-gray-600">&times;</button>
            </div>
            <form action="{{ route('manager.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm">Name (slug)</label>
                        <input name="name" type="text" value="{{ $role->name }}" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Display Name</label>
                        <input name="display_name" type="text" value="{{ $role->display_name }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm">Description</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2" rows="3">{{ $role->description }}</textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editRoleModal-{{ $role->id }}')" class="px-4 py-2 rounded border">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white">Save</button>
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

@if(session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const message = {!! json_encode(session('success') ?? session('error')) !!};
            const type = {!! json_encode(session('error') ? 'error' : 'success') !!};

            // create toast wrapper
            const toastWrap = document.createElement('div');
            toastWrap.setAttribute('role','status');
            toastWrap.className = 'fixed top-5 right-5 z-50';

            const toast = document.createElement('div');
            toast.className = 'px-4 py-3 rounded shadow';
            if(type === 'error'){
                toast.classList.add('bg-red-100','border','border-red-400','text-red-700');
            } else {
                toast.classList.add('bg-green-100','border','border-green-400','text-green-700');
            }
            toast.textContent = message;

            toastWrap.appendChild(toast);
            document.body.appendChild(toastWrap);

            // fade in
            toast.style.opacity = 0;
            toast.style.transition = 'opacity 200ms ease';
            setTimeout(()=> toast.style.opacity = 1, 10);

            // auto hide after 2 seconds (2000ms)
            setTimeout(()=>{
                toast.style.opacity = 0;
                setTimeout(()=> toastWrap.remove(), 250);
            }, 2000);
        });
    </script>
@endif

@endsection
