<div class="row intro-row">
    <div class="col fir-col">
        <img src="{{ user()->avatar_url }}" alt="{{ user()->name }}" class="dash-img user-img-border" width="157"/>
        <div class="intro">
            <div class="hello">
                <h2>{{ __('Hello, :name', ['name' => user()->name]) }}</h2>
            </div>
            <p>{{ __('A Great Day In Your Art Gallery') }}</p>
        </div>
    </div>
</div>

<x-separator-line/>
