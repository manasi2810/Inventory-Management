<button
    type="{{ $type }}"
    class="btn btn-{{ $color }}">

    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif

    {{ $slot }}

</button>