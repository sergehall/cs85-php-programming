@extends($layout ?? 'layouts.app')
{{-- The About page receives personal variables from its closure route. --}}

@section('title', 'About · Personal Route Lab')

@section('content')
    <div class="page-grid">
        <section class="hero" aria-labelledby="about-title">
            <div class="hero-copy">
                <p class="eyebrow">Closure route · Data passed to a view</p>
                <h1 id="about-title">About me</h1>
                <p class="lede">
                    I am a Web Development A.S. student focused on full-stack engineering, thoughtful architecture,
                    and turning what I learn into hands-on projects.
                </p>
            </div>
        </section>

        <section class="profile-layout" aria-label="Personal profile">
            <article class="profile-card">
                <p class="eyebrow">Student profile</p>
                <h2>Learning through real projects</h2>
                <p>
                    I am currently studying Web Development (A.S.) at Santa Monica College. I am focused on full-stack
                    development and applying what I learn through hands-on projects.
                </p>
                <p>
                    My work connects coursework, scalable backend systems, accessible interfaces, and professional
                    photography. This assignment focuses on the request flow from a named route to a Blade response.
                </p>
                <a class="card-link" href="{{ route($routePrefix.'hobbies.index') }}">See the hobbies behind my projects →</a>
            </article>

            <aside class="profile-card" aria-labelledby="profile-facts-title">
                <p class="eyebrow">Route variables</p>
                <h2 id="profile-facts-title">Quick facts</h2>
                <dl class="profile-list">
                    <div>
                        <dt>Age</dt>
                        <dd>{{ $age }}</dd>
                    </div>
                    <div>
                        <dt>School</dt>
                        <dd>{{ $school }}</dd>
                    </div>
                    <div>
                        <dt>Major</dt>
                        <dd>{{ $major }}</dd>
                    </div>
                </dl>
            </aside>
        </section>

        <section class="project-showcase" aria-labelledby="current-work-title">
            <div class="section-header">
                <div>
                    <p class="eyebrow">Current work</p>
                    <h2 id="current-work-title">Projects where I apply what I learn</h2>
                </div>
                <span class="panel-kicker">Full stack · Architecture · Creative</span>
            </div>

            <div class="project-grid">
                <article class="project-card project-card-coursework">
                    <p class="project-type">Coursework platform</p>
                    <h3>
                        <a href="https://webdev-coursework.com/" target="_blank" rel="noopener noreferrer">
                            webdev-coursework.com <span aria-hidden="true">↗</span>
                        </a>
                    </h3>
                    <p>
                        A platform I built to organize and showcase my Web Developer A.S. Degree &amp; Certificate
                        Pathway projects, including this Laravel assignment.
                    </p>
                </article>

                <article class="project-card project-card-lens">
                    <p class="project-type">Active engineering project</p>
                    <h3>
                        <a href="https://lens-lounge.com/" target="_blank" rel="noopener noreferrer">
                            lens-lounge.com <span aria-hidden="true">↗</span>
                        </a>
                    </h3>
                    <p>
                        A modular, event-driven microservices system structured as a modern monorepo with Yarn
                        Workspaces and CI/CD workflows.
                    </p>
                    <ul class="tech-list" aria-label="Lens Lounge technologies">
                        <li>NestJS</li>
                        <li>Go</li>
                        <li>PostgreSQL</li>
                        <li>Kafka</li>
                        <li>React</li>
                    </ul>
                    <p>
                        This project is my hands-on implementation of scalable services with clean boundaries,
                        asynchronous messaging, SOLID principles, Hexagonal Architecture, and event-driven design.
                    </p>
                </article>

                <article class="project-card project-card-photo">
                    <p class="project-type">Professional photography</p>
                    <h3>
                        <a href="https://sergioartg.com/" target="_blank" rel="noopener noreferrer">
                            sergioartg.com <span aria-hidden="true">↗</span>
                        </a>
                    </h3>
                    <p>
                        Outside of technology, I am a professional photographer. I built this portfolio to present
                        my photography, visual work, and creative projects.
                    </p>
                </article>
            </div>

            <aside class="connect-card" aria-labelledby="connect-title">
                <div>
                    <p class="eyebrow">Build · Design · Create</p>
                    <h2 id="connect-title">Let’s connect</h2>
                    <p>
                        I am always open to connecting with people who love to build, design, and create.
                        Explore my repositories and current engineering work on GitHub.
                    </p>
                </div>
                <a class="button" href="https://github.com/SergeHall" target="_blank" rel="noopener noreferrer">
                    Visit github.com/SergeHall <span aria-hidden="true">↗</span>
                </a>
            </aside>
        </section>
    </div>
@endsection
