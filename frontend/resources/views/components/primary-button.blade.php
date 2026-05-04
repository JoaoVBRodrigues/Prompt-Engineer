<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2 bg-emerald-950 border border-emerald-900 rounded-sm font-mono font-medium text-emerald-400 hover:bg-emerald-900 hover:text-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors']) }}>
    [ {{ $slot }} ]
</button>
