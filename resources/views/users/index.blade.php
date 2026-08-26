<x-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <x-page-heading>Users</x-page-heading>
            <x-forms.link-button href="{{ route('users.create') }}">+ Add user</x-forms.link-button>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 outline-none">
                <select name="role" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 outline-none"><option value="">All roles</option>@foreach(['superadmin','manager','employee'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select>
                <select name="status" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 outline-none"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select>
                <div class="md:col-span-4 flex justify-end gap-2"><a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">Reset</a><button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply filters</button></div>
            </form>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            @if($users->count())
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 border-b border-slate-200"><tr><th class="text-left px-5 py-4 font-semibold text-slate-600">User</th><th class="text-left px-5 py-4 font-semibold text-slate-600">Role</th><th class="text-left px-5 py-4 font-semibold text-slate-600">Status</th><th class="text-right px-5 py-4 font-semibold text-slate-600">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
            @foreach($users as $user)<tr class="hover:bg-slate-50"><td class="px-5 py-4"><div class="flex items-center gap-3">@if($user->profile_photo)<img src="{{ asset('storage/'.$user->profile_photo) }}" class="w-10 h-10 rounded-xl object-cover">@else<div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">{{ strtoupper(substr($user->name,0,1)) }}</div>@endif<div><div class="font-semibold text-slate-900">{{ $user->name }}</div><div class="text-slate-500">{{ $user->email }}</div></div></div></td><td class="px-5 py-4"><span class="capitalize rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $user->role }}</span></td><td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ ucfirst($user->status) }}</span></td><td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route('users.edit',$user) }}" class="px-3 py-2 rounded-lg text-indigo-600 hover:bg-indigo-50 font-semibold">Edit</a><form method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('Delete this user? This action cannot be undone.')">@csrf @method('DELETE')<button class="px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 font-semibold">Delete</button></form></div></td></tr>@endforeach
            </tbody></table></div><div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
            @else<div class="p-12 text-center"><div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl">👥</div><h3 class="mt-4 font-semibold text-slate-900">No users found</h3><p class="mt-1 text-sm text-slate-500">Create your first employee or manager account.</p></div>@endif
        </div>
    </div>
</x-layout>
