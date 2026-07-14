# Module 7 Assignment 7B: Basic Routing

Course: CS 85 - PHP Programming  
Student: Siarhei Hancharou

## Objective

This standalone Laravel application is a four-page personal website that demonstrates closure-based routes, controller-based routes, data passed to Blade views, a shared master layout, one dynamic route parameter, and navigation built with named routes. The project also runs inside the main CS85 coursework application so the assignment stays connected to the Module 7 roadmap.

## Project Setup

The project was created with the official Laravel installer:

```bash
laravel new assignments/module7b --no-interaction --phpunit --no-boost --database=sqlite
cd assignments/module7b
php artisan make:controller HobbyController
```

For a fresh clone, install the PHP dependencies and prepare the local environment:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

The local `.env` file is excluded from Git because it may contain environment-specific settings or secrets. The safe `.env.example` template is committed instead.

## Run the Standalone Application

From this assignment folder:

```bash
cd assignments/module7b
php artisan serve --port=8008
```

Open these URLs:

- Home: http://127.0.0.1:8008
- About: http://127.0.0.1:8008/about
- Hobbies: http://127.0.0.1:8008/hobbies
- Hobby detail: http://127.0.0.1:8008/hobbies/1
- Required 404 test: http://127.0.0.1:8008/hobbies/999

## Run Inside the CS85 Coursework Application

From the repository root:

```bash
php artisan serve
```

Open these integrated URLs:

- Module roadmap: http://127.0.0.1:8000/roadmap/module-7
- Home: http://127.0.0.1:8000/assignments/module7b
- About: http://127.0.0.1:8000/assignments/module7b/about
- Hobbies: http://127.0.0.1:8000/assignments/module7b/hobbies
- Hobby detail: http://127.0.0.1:8000/assignments/module7b/hobbies/1

## Route Table

| Method | URI             | Route name      | Handler                 | Purpose                                                        |
| ------ | --------------- | --------------- | ----------------------- | -------------------------------------------------------------- |
| GET    | `/`             | `home`          | Closure                 | Passes my name and the page title to `home.blade.php`.         |
| GET    | `/about`        | `about`         | Closure                 | Passes my student information to `about.blade.php`.            |
| GET    | `/hobbies`      | `hobbies.index` | `HobbyController@index` | Passes the full hobbies array to the index view.               |
| GET    | `/hobbies/{id}` | `hobbies.show`  | `HobbyController@show`  | Finds one hobby by its numeric route parameter or returns 404. |

The route definitions are in `routes/web.php`. The closure routes are useful for the two simple pages because their request logic is short. The hobby routes use `HobbyController` to keep the larger data set and dynamic lookup out of the routes file.

## Blade View Structure

- `resources/views/layouts/app.blade.php` is the master layout. It contains the document structure, responsive styles, accessible navigation, active-link state, and footer.
- `resources/views/home.blade.php` extends the layout and displays variables from the Home closure.
- `resources/views/about.blade.php` extends the layout and displays variables from the About closure.
- `resources/views/hobbies/index.blade.php` loops through the controller data with `@foreach`.
- `resources/views/hobbies/show.blade.php` displays the hobby selected by `{id}`.

All navigation links use Laravel's `route()` helper instead of hard-coded URLs. The current page is identified with `request()->routeIs()`, an `active` class, and `aria-current="page"` for assistive technology.

## Personalization

The website includes my name, Santa Monica College, Web Development (A.S.) program, and three interests connected to my real work: photography, web development, and technology projects. The About page also links to my coursework platform, Lens Lounge microservices project, photography portfolio, and GitHub profile. I chose not to publish a numeric age, so the About route passes the privacy-safe text `Prefer not to disclose`; it still demonstrates the required route-to-view data flow without inventing personal information.

## Testing

Run the standalone test suite:

```bash
composer test
```

Run formatting separately:

```bash
./vendor/bin/pint --test
```

The feature suite verifies the Home and About closure routes, the Hobbies controller route, all three valid dynamic hobby pages, numeric parameter constraints, the `/hobbies/999` 404 response, named route handlers, and active navigation.

## Reflection

### What was the easiest part of this assignment?

The easiest part was personalizing the page content after the routes and views were connected. Blade's escaped `{{ }}` syntax made it simple to display my name, school, major, and hobby fields. The `route()` helper also made the navigation links clear because I could refer to meaningful route names instead of repeating URLs.

### What was the most challenging part?

The most challenging part was understanding the complete request flow for the dynamic hobby page. The `{id}` value starts in the URL, is passed into `HobbyController::show()`, is checked against the hobbies array, and then determines which record Blade receives. Testing `/hobbies/999` helped me confirm why checking that a record exists before displaying it is important.

### What did I learn about Laravel routing?

I learned that a Laravel route connects an HTTP method and URL to the code that prepares a response. A short closure works well for a simple page, while a controller keeps related request logic organized when a feature needs multiple actions or more data. Naming each route also makes navigation easier to maintain because Blade templates do not need to repeat literal URLs.

### Additional concept: How does data move to a Blade view?

The Home and About closures create PHP variables and send them to `view()` in an associative array. Laravel turns the array keys into variables that Blade can display safely with `{{ }}`. The hobby controller uses the same process, but its `index()` method sends the whole array and its `show()` method sends only the record selected by the route parameter.

## Documentation Evidence

The screenshot plan and suggested captions are stored in `docs/screenshots/README.md`. The final submission document should use the filename:

```text
Hancharou_Siarhei_Routing_Documentation.pdf
```

The final project archive should use the filename:

```text
Hancharou_Siarhei_Simple_Website.zip
```

## Submission Checklist

- [x] Standalone Laravel project created.
- [x] Two closure-based routes implemented.
- [x] Two controller-based routes implemented.
- [x] Personal data passed from routes to views.
- [x] Shared master Blade layout created.
- [x] Responsive named-route navigation created with active states.
- [x] Dynamic numeric hobby route implemented.
- [x] Missing hobby ID returns 404.
- [x] Three hobbies personalized.
- [x] Feature tests added.
- [ ] Required screenshots inserted into the documentation PDF.
- [ ] Final PDF and ZIP exported for submission.
- [ ] GitHub push completed after the instructor repository choice is confirmed.
