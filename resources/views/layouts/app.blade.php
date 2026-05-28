<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col bg-gray-100">
        <div class="flex-1">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @include('layouts.footer')
        <div id="globalConfirmModal" class="fixed inset-0 z-[100] hidden">
            <div class="absolute inset-0 bg-black/50" onclick="window.__confirmModalClose?.()"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                <div class="w-full max-w-md rounded-xl bg-white shadow-2xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Aksi</h3>
                        <p id="globalConfirmMessage" class="text-sm text-gray-600 mt-1">Yakin lanjutkan aksi ini?</p>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" id="globalConfirmCancel" class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="button" id="globalConfirmOk" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Ya, lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (() => {
                const modal = document.getElementById('globalConfirmModal');
                const msg = document.getElementById('globalConfirmMessage');
                const btnOk = document.getElementById('globalConfirmOk');
                const btnCancel = document.getElementById('globalConfirmCancel');
                let targetForm = null;
                let bypass = false;

                function open(form, text) {
                    targetForm = form;
                    msg.textContent = text || 'Yakin lanjutkan aksi ini?';
                    modal.classList.remove('hidden');
                    setTimeout(() => btnOk.focus(), 30);
                }

                function close() {
                    modal.classList.add('hidden');
                    targetForm = null;
                }

                window.__confirmModalClose = close;

                document.addEventListener('submit', (e) => {
                    const form = e.target;
                    if (!(form instanceof HTMLFormElement)) return;

                    const confirmText = form.getAttribute('data-confirm');
                    if (!confirmText || bypass) return;

                    e.preventDefault();
                    open(form, confirmText);
                }, true);

                btnCancel?.addEventListener('click', close);

                btnOk?.addEventListener('click', () => {
                    if (!targetForm) return;
                    bypass = true;
                    targetForm.submit();
                    bypass = false;
                    close();
                });

                document.addEventListener('keydown', (e) => {
                    if (modal.classList.contains('hidden')) return;
                    if (e.key === 'Escape') close();
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        btnOk?.click();
                    }
                });
            })();
        </script>
    </body>
</html>
