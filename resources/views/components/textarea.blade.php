<div class="form-group">

    <label>{{ $label }}</label>

    <textarea
        name="{{ $name }}"
        class="form-control">{{ $value ?: old($name) }}</textarea>

    @error($name)
        <span class="text-danger">
            {{ $message }}
        </span>
    @enderror

</div>