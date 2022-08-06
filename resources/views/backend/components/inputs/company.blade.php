@props([
    'value' => null,
    'col' => 'col-12',
    'companies' => \App\Models\Company::all(),
])

@if(user()->isSuperAdmin())
    <div class="{{ $col }}">
        <label class="form-label" for="company_id">{{ __('Company') }}</label>

        <select class="form-control select2 @error('company_id') is-invalid @enderror"
                data-toggle="select2" name="company_id" id="company_id">
            @foreach($companies as $company)
                <x-backend::inputs.select-option :text="$company->name" :value="$company->id" field="company_id"/>
            @endforeach
        </select>
        <x-backend::error field="company_id"/>
    </div>
@endif
