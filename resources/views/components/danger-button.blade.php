<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full py-3 bg-white text-red-600 rounded-md hover:bg-gray-300 focus:bg-gray-700 transition font-bold']) }}>
    {{ $slot }}
</button>
