@extends($layout ?? 'layouts.app')
{{-- HobbyController::index passes the $hobbies array to this template. --}}

@section('title', 'Hobbies · Personal Route Lab')

@section('content')
    <div class="page-grid">
        <section class="hero" aria-labelledby="hobbies-title">
            <div class="hero-copy">
                <p class="eyebrow">Controller route · @@foreach loop</p>
                <h1 id="hobbies-title">My hobbies</h1>
                <p class="lede">
                    These interests combine creativity, technical learning, and hands-on experimentation. Select a card
                    to test the dynamic <code>/hobbies/{id}</code> route.
                </p>
            </div>
        </section>

        <section aria-labelledby="hobby-list-title">
            <div class="section-header">
                <div>
                    <p class="eyebrow">Personalized content</p>
                    <h2 id="hobby-list-title">Three interests I keep developing</h2>
                </div>
                <code class="panel-kicker">hobbies.index</code>
            </div>

            <div class="hobby-grid">
                @foreach ($hobbies as $hobby)
                    <article class="hobby-card">
                        <span class="card-number">0{{ $loop->iteration }}</span>
                        <p class="eyebrow">{{ $hobby['eyebrow'] }}</p>
                        <h2>{{ $hobby['name'] }}</h2>
                        <p>{{ $hobby['description'] }}</p>
                        <a class="card-link" href="{{ route($routePrefix.'hobbies.show', $hobby['id']) }}">
                            Learn more about this hobby →
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        <p><a class="card-link" href="{{ route($routePrefix.'home') }}">← Back to Home</a></p>
    </div>
@endsection
