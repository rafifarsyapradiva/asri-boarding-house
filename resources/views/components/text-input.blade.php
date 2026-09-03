@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-3 py-2.5 bg-white dark:bg-slate-900 text-black dark:text-white font-bold text-sm border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-black dark:focus:border-white disabled:bg-gray-100 dark:disabled:bg-slate-800 disabled:opacity-75 transition-all duration-150']) }}>

