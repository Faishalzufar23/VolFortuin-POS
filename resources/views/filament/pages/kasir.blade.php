<x-filament-panels::page class="!p-0 !m-0">

    <style>
        /* Override Flux Layout (ini yang bikin halaman mengecil) */
        .fi-main,
        .fi-body,
        .fi-page,
        .fi-page-content {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .fi-page-content > div {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        /* Menghilangkan container dan gap bawaan Flux */
        .fi-page {
            display: block !important;
        }
    </style>

    <div class="w-full h-[calc(100vh-4rem)]">
        <iframe
            src="/kasir"
            class="w-full h-full border-0 bg-white"
        ></iframe>
    </div>

</x-filament-panels::page>
