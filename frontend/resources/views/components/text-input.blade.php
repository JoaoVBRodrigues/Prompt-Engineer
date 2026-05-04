@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-900 border-slate-700 text-emerald-400 placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500 focus:ring-1 rounded-sm shadow-inner font-mono']) }}>
