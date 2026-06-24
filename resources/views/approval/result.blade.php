@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-5">
    <div class="card mx-auto text-center" style="max-width: 520px;">
        <div class="card-body py-5">
            @if($status === 'APPROVED')
                <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
            @else
                <i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
            @endif

            <h3 class="mt-4">{{ $title }}</h3>
            <p class="text-muted">{{ $message }}</p>

            <a href="{{ route('transaction.inbIndex') }}" class="btn btn-primary mt-3">
                Back to Inbound
            </a>
        </div>
    </div>
</div>
@endsection