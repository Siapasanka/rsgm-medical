<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Akun</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Buat Akun Baru</h3>

                <form method="POST" action="{{ route('account-users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="username" :value="__('Username')" />
                        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username')" required />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>



                    <div class="md:col-span-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Akun</button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">List Akun</h3>

                <form method="GET" action="{{ route('account-users.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <x-input-label for="q" :value="__('Cari Nama / Username')" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$q" placeholder="Contoh: dr Andi / andi01" />
                    </div>



                    <div class="flex items-end gap-2 mt-6">
                        <button type="submit" class="inline-flex items-center justify-center h-10 border border-blue-700 bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-md text-sm shadow-sm">Filter</button>
                        <a href="{{ route('account-users.index') }}" class="inline-flex items-center justify-center h-10 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 rounded-md text-sm shadow-sm">Reset</a>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Username</th>

                                <th class="px-4 py-2 border">Dibuat Pada</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $user->name }}</td>
                                    <td class="px-4 py-2 border">{{ $user->username }}</td>
                                    <td class="px-4 py-2 border">{{ $user->created_at?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                                            <a href="{{ route('account-users.edit', $user) }}" class="inline-flex items-center h-7 bg-yellow-500 hover:bg-yellow-600 text-white px-2.5 rounded text-xs">Edit</a>

                                            <form class="inline-block" method="POST" action="{{ route('account-users.reset-password', $user) }}" onsubmit="return openResetPasswordModal(this)">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="new_password" value="">
                                                <button type="submit" class="inline-flex items-center h-7 bg-gray-600 hover:bg-gray-700 text-white px-2.5 rounded text-xs border border-gray-700">Reset</button>
                                            </form>

                                            <form class="inline-block" method="POST" action="{{ route('account-users.destroy', $user) }}" data-confirm="Yakin hapus akun ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center h-7 bg-red-600 hover:bg-red-700 text-white px-2.5 rounded text-xs">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 border text-center" colspan="4">Belum ada akun yang bisa dikelola.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="resetPasswordModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeResetPasswordModal()"></div>

        <div class="relative z-10 flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md rounded-xl bg-white shadow-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Reset Password Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">Masukkan password baru minimal 8 karakter.</p>
                </div>

                <div class="p-5 space-y-2">
                    <label for="modal_new_password" class="text-sm font-medium text-gray-700">Password Baru</label>
                    <input id="modal_new_password" type="password" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="••••••••" />
                    <p id="modal_reset_error" class="text-sm text-red-600 hidden">Password minimal 8 karakter.</p>
                </div>

                <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="button" onclick="confirmResetPassword()" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let resetPasswordTargetForm = null;

        function openResetPasswordModal(form) {
            resetPasswordTargetForm = form;

            const modal = document.getElementById('resetPasswordModal');
            const input = document.getElementById('modal_new_password');
            const error = document.getElementById('modal_reset_error');

            input.value = '';
            error.classList.add('hidden');
            modal.classList.remove('hidden');

            setTimeout(() => input.focus(), 50);
            return false;
        }

        function closeResetPasswordModal() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
            resetPasswordTargetForm = null;
        }

        function confirmResetPassword() {
            const input = document.getElementById('modal_new_password');
            const error = document.getElementById('modal_reset_error');
            const newPassword = input.value || '';

            if (newPassword.length < 8) {
                error.classList.remove('hidden');
                input.focus();
                return;
            }

            if (!resetPasswordTargetForm) return;

            resetPasswordTargetForm.querySelector('input[name="new_password"]').value = newPassword;
            resetPasswordTargetForm.submit();
        }

        document.addEventListener('keydown', function (e) {
            const modal = document.getElementById('resetPasswordModal');
            if (modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') {
                closeResetPasswordModal();
            }

            if (e.key === 'Enter') {
                e.preventDefault();
                confirmResetPassword();
            }
        });
    </script>
</x-app-layout>
