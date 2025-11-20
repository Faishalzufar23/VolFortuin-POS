<x-filament-panels::page class="!p-0 !m-0">

    <style>
        .fi-main,
        .fi-body,
        .fi-page,
        .fi-page-content {
            max-width: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .fi-page-content > div {
            width: 100% ;
            max-width: 100% ;
            padding: 0 ;

        }

        .fi-page {
            display: block ;
        }

        .w-full {
            width: 100%;
            height: max-content;
            max-width: 100%;
            padding: 0;
            background: green;
        }
    </style>

    <div class="w-full">
        <iframe
            src="/kasir"
            class="w-full h-full border-0 bg-white"
            style="height: 1000px; background">
        </iframe>
    </div>

</x-filament-panels::page>
