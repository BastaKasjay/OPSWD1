@extends('layouts.app')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <div class="mt-4 flex items-center justify-between">
            <button type="submit" class="btn btn-primary">
                {{ __('Resend Verification Email') }}
            </button>

            <a href="{{ route('logout') }}" class="underline text-sm text-gray-600 hover:text-gray-900" onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Log Out') }}
            </a>
        </div>
    </form>
@endsection
