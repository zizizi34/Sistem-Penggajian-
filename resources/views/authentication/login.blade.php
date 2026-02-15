@extends('authentication.layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="login-container">
  <!-- Left Section -->
  <div class="login-left">
    <div class="login-form-wrapper">
      <!-- Logo -->
      <div class="login-logo">
        <img src="{{ asset('images/logo/laguna.png') }}" alt="Laguna Logo">
      </div>

      <!-- Title -->
      <h1 class="login-title">Masuk</h1>
      <p class="login-subtitle">Masuk untuk melanjutkan</p>

      <!-- Alert Messages -->
      @include('utilities.alert')

      <!-- Login Form -->
      <form action="{{ route('login') }}" method="POST">
        @csrf

        <!-- Email Input -->
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email"
            name="email" 
            class="form-control @error('email', 'authentication') is-invalid @enderror" 
            placeholder="Masukkan email Anda"
            value="{{ old('email') }}" 
            autofocus 
            required 
          />
          @error('email', 'authentication')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Password Input with Eye Icon -->
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-input-wrapper" style="position: relative;">
            <input 
              type="password" 
              id="password"
              name="password" 
              class="form-control @error('password', 'authentication') is-invalid @enderror" 
              placeholder="Masukkan password Anda"
              required 
            />
            <button 
              type="button" 
              class="btn-toggle-password" 
              onclick="togglePasswordVisibility()"
              style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6c757d;"
            >
              <i class="bi bi-eye" id="password-icon"></i>
            </button>
          </div>
          @error('password', 'authentication')
          <div class="invalid-feedback" style="display: block;">
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="login-btn">
          Masuk
        </button>
      </form>

      <!-- Footer -->
      <div class="login-footer">
        <p class="login-footer-text">
          <strong>Laguna Group</strong>
        </p>
      </div>
    </div>
  </div>

  <!-- Right Section - Illustration -->
  <div class="login-right">
    <div class="right-content">
      <div class="right-logo">
        <img src="{{ asset('images/logo/laguna.png') }}" alt="Laguna Logo">
      </div>
      <h2>Laguna Group</h2>
      <p>Sistem manajemen penggajian terpadu untuk efisiensi maksimal</p>
    </div>
  </div>
</div>

@push('script')
<script>
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.classList.remove('bi-eye');
      passwordIcon.classList.add('bi-eye-slash');
    } else {
      passwordInput.type = 'password';
      passwordIcon.classList.remove('bi-eye-slash');
      passwordIcon.classList.add('bi-eye');
    }
  }
</script>
@endpush
@endsection
