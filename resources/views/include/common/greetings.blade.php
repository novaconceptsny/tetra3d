<div class="row intro-row">
    <div class="col fir-col">
        <img src="{{ user()->avatar_url }}" alt="{{ user()->name }}" class="dash-img" width="157"/>
        <div class="intro">
            <div class="hello">
                <h2>{{ __('Hello, :name', ['name' => user()->name]) }}</h2>
                <img src="{{ asset('redesign/images/Group.png') }}" alt="hand-img" />
            </div>
            <p>{{ __('A Great Day In Your Art Gallery') }}</p>
        </div>
        <a href="#" class="sorted-btn">
            <img src="{{ asset('redesign/images/material-symbols_grid-view-outline.png') }}" alt=""/>
            {{ __('Sort By Date') }}
        </a>
    </div>
</div>

<div class="my-5">
    <x-separator-line/>
</div>
