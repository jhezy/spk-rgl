@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100% bg-light">
    <div class="card shadow-sm border-0 rounded-4" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="fw-semibold mb-1">Selamat Datang </h4>
                <p class="text-muted small mb-0">Masuk untuk melanjutkan ke dashboard</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="you@example.com">

                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="••••••••">

                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                    <a class="text-decoration-none small fw-semibold text-primary"
                        href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                    @endif
                </div>

                {{-- Tombol Login --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold">
                        Masuk
                    </button>
                </div>
            </form>

            {{-- Footer --}}
            <div class="text-center mt-4 small text-muted">
                Belum punya akun?
                <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">
                    Daftar sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection