# Module 12 Assignment 12A: Integrating OpenAI

This course-aligned implementation is a Laravel content generator powered by
the OpenAI Chat Completions API and the `gpt-4o-mini` model. A user supplies a
title, selects a content type and tone, and receives an editable draft.

The assignment is intentionally separate from the larger Final Project AI
workspace. This keeps every rubric requirement easy to locate while showing a
clear path from a single API request to a production-oriented AI system.

## Verified OpenAI API Integration

Assignment 12A is connected to the live OpenAI API through Laravel's
server-side HTTP client. The complete browser flow has been verified: the form
submitted a structured prompt, OpenAI processed it with `gpt-4o-mini`, and the
returned content appeared in the editable result textarea.

- Provider: OpenAI API
- Endpoint: `POST https://api.openai.com/v1/chat/completions`
- Model: `gpt-4o-mini`
- Secret boundary: `OPENAI_API_KEY` stays in the local `.env` file
- Browser boundary: the browser posts only assignment inputs to Laravel
- Verified result: a generated meta description was returned successfully

Live browser checks use API credits. Automated tests continue to use fakes, so
CI and local test runs do not contact OpenAI or incur API charges.

## Working Routes

- `GET /ai-form` - display the content generator
- `POST /ai-generate` - validate input and generate a draft
- `GET /roadmap/module-12` - compare Assignment 12A with the Final Project
- `GET /cabinet/ai` - open the authenticated Final Project workspace

## Required Files

- `app/Http/Controllers/AiContentController.php`
- `app/Services/AiContentService.php`
- `resources/views/ai_form.blade.php`
- `config/services.php`
- `.env.example`
- `routes/web.php`
- `tests/Feature/Module12AiContentAssignmentTest.php`
- `tests/Unit/AI/Module12AiContentServiceTest.php`

## How the Assignment Works

1. The Blade form posts a title, content type, and tone.
2. `AiContentController` validates all three values.
3. `AiContentService::buildPrompt()` selects the task, tone instructions,
   length, and format.
4. Laravel sends a server-side request to
   `POST https://api.openai.com/v1/chat/completions`.
5. The service extracts `choices[0].message.content`.
6. The generated text is returned to an editable textarea.
7. Validation and provider failures preserve the form and display a safe
   message.

## Prompt Variants

| Content type       | Prompt constraint                                               |
| ------------------ | --------------------------------------------------------------- |
| Blog post          | 350-500 words, Markdown, introduction, headings, and conclusion |
| Meta description   | One line, approximately 150-160 characters                      |
| Email subject line | One line, 6-10 words, no more than 60 characters                |

| Tone         | Model role                                                  |
| ------------ | ----------------------------------------------------------- |
| Professional | Professional content strategist                             |
| Casual       | Friendly digital copywriter                                 |
| Humorous     | Witty copywriter who keeps the result useful and respectful |

## Local Setup

### macOS with Laravel Herd

```bash
git clone https://github.com/SergeHall/cs85-php-programming.git
cd cs85-php-programming
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Add the OpenAI settings to `.env`:

```dotenv
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_API_URL=https://api.openai.com/v1
OPENAI_MODEL=gpt-4o-mini
OPENAI_CONNECT_TIMEOUT=5
OPENAI_REQUEST_TIMEOUT=30
```

Run the asset build and open the Herd URL:

```bash
npm run build
```

Visit `/ai-form` on the local Herd domain.

### Windows

Run the same commands in Git Bash or the VS Code terminal. If Laravel Herd is
not serving the directory, start Laravel directly:

```bash
php artisan serve
npm run dev:assets
```

Then visit `http://127.0.0.1:8000/ai-form`.

## Obtaining an OpenAI API Key

Create and manage API keys in the OpenAI Platform account. Store the key only
in the local `.env` file. Never place it in PHP, Blade, JavaScript, screenshots,
commits, or GitHub Actions logs.

The browser never calls OpenAI directly. Laravel reads the key through
`config('services.openai.key')` and sends the request from the server.

## Testing Without API Credits

The test suite never calls the real OpenAI API:

```bash
php artisan test --filter=Module12
```

The feature test replaces `AiContentService` with a deterministic fake. The unit tests use
Laravel `Http::fake()` to verify the endpoint, authorization header, model,
roles, prompt constraints, success response, and provider failure.

## Reflection

### 1. How did the output change when the tone or role changed?

The professional role produces precise and publication-oriented language. The
casual role uses shorter, more conversational phrasing. The humorous role adds
playful wording while the prompt explicitly preserves clarity and respect.
Changing the role influences the voice before the model processes the
content-specific task.

### 2. How did the prompt differ across the three content types, and why?

The blog prompt requests several hundred words, Markdown headings, supporting
details, and a conclusion because a long-form draft needs structure. The meta
description prompt enforces a one-line character target because search snippets
have limited space. The email subject prompt uses a short word and character
limit because inbox scanning rewards clarity and brevity.

### 3. What would improve this integration for production?

A production version should require authentication, apply user quotas, track
latency and token usage, support provider switching, stream longer responses,
validate structured outputs, redact sensitive input, evaluate prompt quality,
and provide a graceful fallback. Those improvements are demonstrated by the
separate Final Project implementation at `/cabinet/ai`.

## Submission Evidence

Add these screenshots before submission:

1. The complete `/ai-form` page before generation.
2. A generated draft visible in the editable textarea.
3. `AiContentService::generateDraft()` and `buildPrompt()` in VS Code.
4. `AiContentController::generate()` validation and error handling.
5. A passing `php artisan test --filter=Module12` terminal result.

## GitHub

- [Completed Assignment 12A](https://github.com/SergeHall/cs85-php-programming/tree/main/assignments/module12a)
- [Module 12 implementation source](https://github.com/SergeHall/cs85-php-programming/tree/main/app/Services)
- [AI architecture documentation](https://github.com/SergeHall/cs85-php-programming/tree/main/docs/architecture)
