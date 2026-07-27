# Assignment 11A: API Data

Module 11 contains two connected implementations:

1. The course-aligned static JSON assignment.
2. A production-oriented live API extension.

## URLs

- Course-aligned weather assignment: `http://127.0.0.1:8000/weather`
- Advanced API workbench: `http://127.0.0.1:8000/roadmap/module-11`
- Advanced normalized JSON response: `http://127.0.0.1:8000/roadmap/module-11/data`
- Advanced external provider: `https://jsonplaceholder.typicode.com/users`

## Course-Aligned Track

The required exercise simulates an API response with `storage/app/private/weather.json`.

`WeatherController`:

1. Reads the file with `Storage::get('weather.json')`.
2. Decodes the JSON string with `json_decode($json, true)`.
3. Passes the associative array to `weather.index`.
4. Lets Blade render each record with `@foreach`.

The table includes the optional enhancements: rainy-day conditional formatting and alphabetical sorting.

### Required Files

| File                                         | Purpose                     |
| -------------------------------------------- | --------------------------- |
| `storage/app/private/weather.json`           | Static JSON API simulation  |
| `app/Http/Controllers/WeatherController.php` | Read, decode, and pass data |
| `resources/views/weather/index.blade.php`    | Styled Blade weather table  |
| `routes/web.php`                             | Register `GET /weather`     |

## Advanced Track

The advanced page fetches JSONPlaceholder users on the Laravel server, converts valid records into typed `ApiContact` objects, supports validated search and sort controls, caches successful responses, and uses a versioned fallback dataset when the provider cannot be reached.

## Clean Architecture Map

| Layer          | Owner                                  | Responsibility                                        |
| -------------- | -------------------------------------- | ----------------------------------------------------- |
| Presentation   | `Module11AApiDataController` and Blade | Coordinate the request and render HTML or JSON        |
| Application    | `BrowseApiContacts`                    | Search, sort, limit, and summarize contacts           |
| Domain         | `ApiContact`                           | Define the trusted application data contract          |
| Infrastructure | `JsonPlaceholderUserClient`            | Perform HTTP, cache, validate, and load fallback data |

## Security and Reliability

- The course track stores the static JSON file on Laravel's private local disk.
- The API endpoint is configured server-side; visitors cannot submit an arbitrary URL.
- Query inputs use a Laravel Form Request and allowlisted sort and limit values.
- The remote request uses HTTPS, an `Accept: application/json` header, and a short timeout.
- Successful responses are cached for ten minutes.
- Invalid API records are discarded at the normalization boundary.
- Blade escapes all displayed provider values.
- A local JSON fixture keeps the assignment available during an outage.
- Feature tests use `Http::fake()` and never depend on the live provider.

## Run and Test

```bash
php artisan serve
php artisan test --filter=Module11ApiDataTest
```

## Reflection

The course track shows the core transformation directly: JSON text becomes a PHP associative array, the controller passes that data to a view, and Blade loops over it.

The advanced track adds the boundaries needed for a remote provider. An external API response is untrusted input, not an application model. Mapping the provider response into a small typed object gives the controller, JSON endpoint, and Blade view one stable contract. The infrastructure layer can change providers or fallback behavior without moving HTTP concerns into the rest of the application.

Caching and fallback behavior solve different problems. Caching reduces latency and provider traffic after a successful request. The fallback keeps the page demonstrable when there is no successful response available. Both behaviors are visible in the page status so the interface does not pretend fallback data is live.

## Screenshot Checklist

1. Browser: the complete `/weather` forecast table.
2. Browser: the Module 11 advanced hero and API response status.
3. Browser: filtered API contact cards.
4. Browser: the normalized `/roadmap/module-11/data` JSON response.
5. VS Code: `weather.json`, `WeatherController`, and `weather.index`.
6. VS Code: the advanced controller, application service, domain DTO, and infrastructure client.
7. Terminal: a passing `Module11ApiDataTest` run.
