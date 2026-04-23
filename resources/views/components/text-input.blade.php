@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl text-sm transition', 'style' => 'border:1px solid #d6cfbe; background:white; color:#1a3327; padding:0.625rem 1rem; width:100%;', 'onfocus' => "this.style.borderColor='#1a3327'; this.style.outline='none'", 'onblur' => "this.style.borderColor='#d6cfbe'"]) }}>
