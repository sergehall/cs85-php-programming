@extends($layout ?? 'layouts.app')
{{-- The home template extends the shared master layout. --}}

@section('title', 'Home · Personal Route Lab')

@section('content')
    <div class="page-grid">
        <section class="hero" aria-labelledby="home-title">
            <div class="hero-copy">
                <p class="eyebrow">Module 7 Assignment 7B · Basic Routing</p>
                <h1 id="home-title">{{ $title }}</h1>
                <p class="lede">
                    Hello! My name is <strong>{{ $name }}</strong>. This four-page Laravel website demonstrates
                    closure routes, a controller, Blade inheritance, route parameters, and named navigation.
                </p>
                <div class="actions">
                    <a class="button" href="{{ route($routePrefix.'hobbies.index') }}">Explore my hobbies</a>
                    <a class="button button-secondary" href="{{ route($routePrefix.'about') }}">Learn more about me</a>
                </div>
            </div>
        </section>

        <section aria-labelledby="route-map-title">
            <div class="section-header">
                <div>
                    <p class="eyebrow">Request map</p>
                    <h2 id="route-map-title">Four pages, two routing styles</h2>
                </div>
                <span class="panel-kicker">GET requests</span>
            </div>

            <div class="panel-grid">
                <article class="panel">
                    <span class="panel-kicker">Closure</span>
                    <h3>Home</h3>
                    <p>The route prepares my name and page title, then passes both variables into this Blade view.</p>
                    <code class="route-code">GET / · home</code>
                </article>

                <article class="panel">
                    <span class="panel-kicker">Closure</span>
                    <h3>About</h3>
                    <p>A second anonymous function sends personal school and program information to the About view.</p>
                    <code class="route-code">GET /about · about</code>
                </article>

                <article class="panel">
                    <span class="panel-kicker">Controller</span>
                    <h3>Hobbies</h3>
                    <p>HobbyController organizes the list and dynamic detail responses with index and show methods.</p>
                    <code class="route-code">GET /hobbies/{id}</code>
                </article>
            </div>
        </section>
    </div>
@endsection
