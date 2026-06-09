<div class="form-group">

    <label>{{ $label }}</label>

    <select
        name="{{ $name }}"
        class="form-control">

        @foreach($options as $key => $value)

            <option
                value="{{ $key }}"
                {{ $selected == $key ? 'selected' : '' }}>

                {{ $value }}

            </option>

        @endforeach

    </select>

</div>