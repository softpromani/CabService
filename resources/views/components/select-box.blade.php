<div>
    <div class="mb-3">
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
        <select name="{{ $name }}{{ $multiple ? '[]' : '' }}" id="{{ $name }}" 
            class="form-select" {{ $multiple ? 'multiple' : '' }}>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error($name)
        <div class="invalid-feedback d-block">
        {{ $message }}
        </div>
    @enderror
    </div>
</div>