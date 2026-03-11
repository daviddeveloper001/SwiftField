@props(['disabled' => false, 'error' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full ' .
        ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''),
]) !!}>
    {{ $slot }}
</select>
