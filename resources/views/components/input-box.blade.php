<div>
    <div class="col-md-12">
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
        <input type="{{ $type }}" class="
            @if($type=='checkbox') form-check-input @else form-control @endif
            @error($name) is-invalid @enderror" id="{{ $name }}"
            name="{{ $name }}"
            @isset($value) value="{{ $value }}" @endisset
            @isset($required) required @endisset
            @isset($pattern) pattern="{{ $pattern }}" @endisset
            @isset($checked) checked="{{ $checked }}" @endisset
        >
        @error($name)
            <div class="invalid-feedback d-block">
            {{ $message }}
            </div>
        @enderror
    </div>
</div>
