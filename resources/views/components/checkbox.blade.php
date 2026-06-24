<div class="form-check">

    <input type="checkbox"
           name="{{ $name }}"
           value="{{ $value }}"
           id="{{ $id }}"
           class="form-check-input"
           @checked($checked ?? false)>

    <label class="form-check-label" for="{{ $id }}">
        {{ $label }}
    </label>

</div>