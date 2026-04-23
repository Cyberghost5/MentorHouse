<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition', 'style' => 'background:#1a3327; color:#f4f1e8;', 'onmouseover' => "this.style.background='#0f2219'", 'onmouseout' => "this.style.background='#1a3327'", 'onfocus' => "this.style.outline='2px solid #c49a3c'"]) }}>
    {{ $slot }}
</button>
