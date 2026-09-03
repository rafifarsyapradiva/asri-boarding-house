<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 min-h-[44px] bg-yellow-400 hover:bg-yellow-300 active:bg-yellow-500 text-black font-black text-xs uppercase tracking-wider border-4 border-black shadow-[4px_4px_0px_0px_#000000] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none focus:outline-none focus-visible:outline-3 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2 transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>

