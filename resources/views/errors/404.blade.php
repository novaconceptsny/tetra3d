@extends('layouts.redesign')

@section('content')
<div class="container-fluid" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
    <div class="text-center">
        <div class="error-container" style="max-width: 500px; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="error-icon" style="font-size: 80px; color: #dc3545; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 style="font-size: 48px; color: #343a40; margin-bottom: 10px; font-weight: 300;">404</h1>
            
            <div class="error-message" style="margin-bottom: 30px;">
                <h2 style="color: #dc3545; font-size: 24px; margin-bottom: 15px; font-weight: 500;">
                    @if(isset($exception) && $exception->getMessage())
                        {{ $exception->getMessage() }}
                    @else
                        Page Not Found
                    @endif
                </h2>
                
                <p style="color: #6c757d; font-size: 16px; line-height: 1.6;">
                    @if(isset($exception) && $exception->getMessage() === 'This link has been disabled')
                        This shared tour link has been disabled by the administrator. 
                        Please contact the tour owner for access or try a different link.
                    @elseif(isset($exception) && $exception->getMessage() === 'The tour doesn\'t exist')
                        The requested tour could not be found. 
                        The tour may have been deleted or the link may be incorrect.
                    @elseif(isset($exception) && $exception->getMessage() === 'Tour not found or access denied')
                        The tour you're trying to access is not available or you don't have permission to view it.
                        Please contact the tour administrator for access.
                    @elseif(isset($exception) && $exception->getMessage() === 'Project not found or access denied')
                        The project associated with this tour is not available or you don't have permission to view it.
                        Please contact the project administrator for access.
                    @elseif(isset($exception) && $exception->getMessage() === 'Invalid spot access')
                        The specific spot you're trying to access is not available in this shared tour.
                        Please try accessing the tour without specifying a spot.
                    @else
                        The page you're looking for doesn't exist or has been moved.
                    @endif
                </p>
            </div>
            
            <div class="error-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary" style="padding: 12px 24px; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 