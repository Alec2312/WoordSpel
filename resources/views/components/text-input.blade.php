@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'placeholder-black border-black border-2 rounded-md shadow-sm bg-[#FEC70C]']) }}>
