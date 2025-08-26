<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <!-- bootstrap css link  -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <!-- own css file  -->
    <link rel="stylesheet" href="{{ asset('redesign/css/style.css') }}"/>
    @include('backend.includes.partial.favicon')
</head>
<body class="login_body">

<main class="login">

    <div class="container-fluid">
        <div class="row login-row">
            <div class="inner-div col-lg-4">
                <div class="logo">
                    <img width="200" src="{{ asset('backend/images/logo/logo_dark.png') }}" alt="logo-img"/>
                </div>
                <div class="fir-inner">
                    <h4 class="login d-flex align-items-center justify-content-center">
                        {{ __('Register') }}
                    </h4>
                    <p class="text-center">
                        {{ __('Create your account to get started') }}
                    </p>
                    <form class="d-flex flex-column align-items-center" method="POST" action="{{ route('register') }}">
                        @csrf
                        @if(request('redirect'))
                            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                        @endif
                        <div class="form-group login-custum-form-group">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <input type="text" id="first_name" placeholder="First Name"
                                   class="form-control @error('first_name') is-invalid @enderror" name="first_name"
                                   value="{{ old('first_name') }}" required autocomplete="given-name" autofocus
                            />
                            <x-error field="first_name"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="last_name">{{ __('Last Name') }}</label>
                            <input type="text" id="last_name" placeholder="Last Name"
                                   class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                                   value="{{ old('last_name') }}" required autocomplete="family-name"
                            />
                            <x-error field="last_name"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="company_name">{{ __('Company') }}</label>
                            <input type="text" id="company_name" placeholder="Type company name"
                                   class="form-control @error('company_name') is-invalid @enderror" name="company_name"
                                   value="{{ old('company_name') }}" required autocomplete="off"
                            />
                            <input type="hidden" id="company_id" name="company_id" value="{{ old('company_id') }}">
                            <div id="company-suggestions" class="suggestions-dropdown" style="display: none;"></div>
                            <x-error field="company_name"/>
                            <x-error field="company_id"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" id="email" placeholder="Email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email"
                            />
                            <x-error field="email"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input
                                placeholder="Password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" type="password"
                                required autocomplete="new-password"
                            >
                            <x-error field="password"/>
                        </div>
                        <div class="form-group login-custum-form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input
                                placeholder="Confirm Password"
                                class="form-control"
                                name="password_confirmation" id="password-confirm" type="password"
                                required autocomplete="new-password"
                            >
                        </div>
                        <button type="submit" class="btn-login btn form-control">Register</button>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                By registering, you agree to receive a verification code via email to complete your account setup.
                            </small>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <p class="m-0">
                                {{ __('Already have an account?') }} 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                    {{ __('Sign in here') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-8 sec-col">
                <img src="{{asset('redesign/images/login-hero-banner.gif')}}" alt="login-page-img">
            </div>
        </div>
    </div>
</main>

<!-- bootstrap script links -->
<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
    integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
    crossorigin="anonymous"
></script>

<script src="{{ asset('js/bootstrap.min.js') }}"></script>

<style>
.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item.new-company {
    background-color: #e8f5e8;
    font-style: italic;
}

.suggestion-item.new-company:hover {
    background-color: #d4edda;
}

.form-group {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyInput = document.getElementById('company_name');
    const companyIdInput = document.getElementById('company_id');
    const suggestionsDiv = document.getElementById('company-suggestions');
    let selectedIndex = -1;
    let suggestions = [];

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search companies
    const searchCompanies = debounce(function(query) {
        if (query.length < 2) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        fetch(`/api/companies/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestions = data;
                displaySuggestions(query);
            })
            .catch(error => {
                console.error('Error searching companies:', error);
            });
    }, 300);

    // Display suggestions
    function displaySuggestions(query) {
        if (suggestions.length === 0) {
            // Show option to create new company
            suggestionsDiv.innerHTML = `
                <div class="suggestion-item new-company" data-action="create" data-name="${query}">
                    Create new company: "${query}"
                </div>
            `;
        } else {
            let html = '';
            
            // Add existing companies
            suggestions.forEach(company => {
                html += `
                    <div class="suggestion-item" data-id="${company.id}" data-name="${company.name}">
                        ${company.name}
                    </div>
                `;
            });
            
            // Add option to create new company if it doesn't exist
            const exactMatch = suggestions.find(company => 
                company.name.toLowerCase() === query.toLowerCase()
            );
            
            if (!exactMatch) {
                html += `
                    <div class="suggestion-item new-company" data-action="create" data-name="${query}">
                        Create new company: "${query}"
                    </div>
                `;
            }
            
            suggestionsDiv.innerHTML = html;
        }
        
        suggestionsDiv.style.display = 'block';
        selectedIndex = -1;
    }

    // Handle input events
    companyInput.addEventListener('input', function() {
        const query = this.value.trim();
        companyIdInput.value = ''; // Clear company ID when user types
        searchCompanies(query);
    });

    // Handle keyboard navigation
    companyInput.addEventListener('keydown', function(e) {
        const items = suggestionsDiv.querySelectorAll('.suggestion-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                selectItem(items[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            suggestionsDiv.style.display = 'none';
            selectedIndex = -1;
        }
    });

    // Update selection highlighting
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.style.backgroundColor = '#007bff';
                item.style.color = 'white';
            } else {
                item.style.backgroundColor = '';
                item.style.color = '';
            }
        });
    }

    // Handle suggestion clicks
    suggestionsDiv.addEventListener('click', function(e) {
        if (e.target.classList.contains('suggestion-item')) {
            selectItem(e.target);
        }
    });

    // Select an item
    function selectItem(item) {
        const action = item.dataset.action;
        
        if (action === 'create') {
            // Create new company
            const companyName = item.dataset.name;
            createCompany(companyName);
        } else {
            // Select existing company
            const companyId = item.dataset.id;
            const companyName = item.dataset.name;
            
            companyInput.value = companyName;
            companyIdInput.value = companyId;
            suggestionsDiv.style.display = 'none';
        }
    }

    // Create new company
    function createCompany(name) {
        fetch('/api/companies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                companyInput.value = data.company.name;
                companyIdInput.value = data.company.id;
                suggestionsDiv.style.display = 'none';
                
                // Show a subtle message if company already existed
                if (response.status === 200) { // 200 means existing company was found
                    console.log('Using existing company: ' + data.company.name);
                }
            } else {
                alert('Failed to create company. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error creating company:', error);
            alert('Failed to create company. Please try again.');
        });
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!companyInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
            selectedIndex = -1;
        }
    });
});
</script>
</body>
</html>
