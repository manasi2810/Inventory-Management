<div class="form-group">

    <label>{{ $label }}</label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value ?? '') }}"
        class="form-control">

    @error($name)
        <span class="text-danger">
            {{ $message }}
        </span>
    @enderror

</div>