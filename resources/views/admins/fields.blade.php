<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

    @error('nombre')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
        <i class="ik ik-user"></i>
    </div>
    <div class="form-group">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

    @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
        <i class="ik ik-mail"></i>
    </div>
    <div class="form-group">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <i class="ik ik-lock"></i>
    </div>
    <div class="form-group">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        <i class="ik ik-eye-off"></i>
    </div>
    {{-- <div class="row">
        <div class="col-12 text-left">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                <span class="custom-control-label">&nbsp;Acepto <a href="#">los terminos y condiciones.</a></span>
            </label>
        </div>
    </div> --}}
    <div class="sign-btn text-center">
        <button type="submit" class="btn btn-theme">{{ __('Crear Cuenta') }}</button>
    </div>
</form>
