@if(session('verification_warning') || (auth()->check() && !auth()->user()->is_verified))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div>
                <strong>Email Verification Required</strong>
                <p class="mb-0 mt-1">
                    @if(session('verification_warning'))
                        {{ session('verification_warning') }}
                    @else
                        Please verify your email address to access all features and ensure account security.
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-primary me-2">
                Verify Email
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="alert">
                Dismiss
            </button>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif 