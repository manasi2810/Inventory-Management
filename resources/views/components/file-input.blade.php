<div class="form-group">

    <label>{{ $label }}</label>

    <input
        type="file"
        name="{{ $name }}"
        class="form-control"
        {{ $multiple ? 'multiple' : '' }}>

</div>