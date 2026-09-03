@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-400 text-black border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
        <div class="flex items-center">
            <span class="mr-2 font-black text-lg">✅</span>
            <span class="font-black text-sm uppercase tracking-wider">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-400 text-black border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
        <div class="flex items-center">
            <span class="mr-2 font-black text-lg">❌</span>
            <span class="font-black text-sm uppercase tracking-wider">{{ session('error') }}</span>
        </div>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="mb-6 p-4 bg-red-400 dark:bg-red-950 text-black dark:text-red-200 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
        <div class="font-black text-sm mb-1 uppercase tracking-wider">⚠️ Terjadi kesalahan input:</div>
        <ul class="list-disc pl-5 text-xs font-bold space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
