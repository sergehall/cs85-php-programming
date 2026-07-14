@extends($layout ?? 'layouts.app')
{{-- HobbyController::show passes one hobby selected by the {id} parameter. --}}

@section('title', $hobby['name'].' · Personal Route Lab')

@section('content')
    <div class="page-grid">
        <section class="hero" aria-labelledby="hobby-title">
            <div class="hero-copy">
                <p class="eyebrow">Dynamic route · Hobby {{ $hobby['id'] }}</p>
                <h1 id="hobby-title">{{ $hobby['name'] }}</h1>
                <p class="lede">{{ $hobby['eyebrow'] }}</p>
                <div class="actions">
                    <a class="button" href="{{ route($routePrefix.'hobbies.index') }}">Back to all hobbies</a>
                    <a class="button button-secondary" href="{{ route($routePrefix.'home') }}">Back to Home</a>
                </div>
            </div>
        </section>

        <section class="detail-wrap" aria-label="Hobby details">
            <article class="detail-card">
                <h2>What is it?</h2>
                <p>{{ $hobby['description'] }}</p>

                <h2>Why I like it</h2>
                <p>{{ $hobby['why_i_like_it'] }}</p>

                <h2>How it connects to my work</h2>
                <p>{{ $hobby['detail'] }}</p>
            </article>

            <aside class="route-facts" aria-label="Laravel route facts">
                <div class="fact">
                    <span>HTTP method</span>
                    <strong>GET</strong>
                </div>
                <div class="fact">
                    <span>Dynamic URL</span>
                    <strong>/hobbies/{{ $hobby['id'] }}</strong>
                </div>
                <div class="fact">
                    <span>Named route</span>
                    <strong>hobbies.show</strong>
                </div>
                <div class="fact">
                    <span>Controller method</span>
                    <strong>HobbyController::show({{ $hobby['id'] }})</strong>
                </div>
            </aside>
        </section>
    </div>
@endsection
