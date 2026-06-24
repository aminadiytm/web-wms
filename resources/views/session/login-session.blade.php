@extends('layouts.user_type.guest')

@section('content')

<main class="main-content mt-0">
  <section class="min-vh-100 d-flex align-items-center">
    <div class="container">
      <div class="row shadow-lg border-radius-xl overflow-hidden mt-3">

        {{-- LEFT SIDE - FORM --}}
        <div class="col-lg-5 col-md-6 bg-white p-5">
          
          <div class="mb-4">
            <h2 class="font-weight-bold text-dark mb-1">Welcome!</h2>
            <p class="text-muted mb-0">Sign in to continue to your dashboard</p>
          </div>

          <form role="form" method="POST" action="/session">
            @csrf

            {{-- Email --}}
            <div class="form-group mb-4">
              <label class="form-label text-sm">Email Address</label>
              <div class="input-group input-group-outline">
                <input 
                  type="email" 
                  class="form-control" 
                  name="email" 
                  id="email"
                  placeholder="Enter your email"
                  value="admin@gmail.com"
                >
              </div>
              @error('email')
                <p class="text-danger text-xs mt-2">{{ $message }}</p>
              @enderror
            </div>

            {{-- Password --}}
            <div class="form-group mb-3">
              <label class="form-label text-sm">Password</label>
              <div class="input-group input-group-outline">
                <input 
                  type="password" 
                  class="form-control" 
                  name="password" 
                  id="password"
                  placeholder="Enter your password"
                  value="Admin#123"
                >
              </div>
              @error('password')
                <p class="text-danger text-xs mt-2">{{ $message }}</p>
              @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                <label class="form-check-label text-sm" for="rememberMe">Remember me</label>
              </div>

            </div>

            {{-- Submit --}}
            <div class="d-grid">
              <button type="submit" class="btn bg-gradient-primary btn-lg w-100 mb-3">
                Sign In
              </button>
            </div>

          </form>

        </div>

        {{-- RIGHT SIDE - VISUAL --}}
        <div class="col-lg-7 d-none d-lg-block position-relative p-0">
          <div class="h-100 w-100 bg-cover bg-center position-absolute"
               style="background-image: url('../assets/img/curved-images/background_login.jpg');">
          </div>

          <div class="position-absolute h-100 w-100 bg-gradient-dark opacity-6"></div>

          {{-- <div class="position-relative z-index-1 h-100 d-flex align-items-center justify-content-center text-center text-white p-5">
            <div>
              <h2 class="font-weight-bold">Your Productivity Hub</h2>
              <p class="opacity-8">
                Manage your data, track performance, and collaborate seamlessly in one place.
              </p>
            </div>
          </div> --}}
        </div>

      </div>
    </div>
  </section>
</main>

@endsection