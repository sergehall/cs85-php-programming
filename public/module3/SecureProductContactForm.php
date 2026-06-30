<?php
/*
 * Module 3 Assignment 3B: Secure Product Contact Form
 * Student: Serge Hall
 * GitHub Repository: cs85-module3b-createform
 *
 * Output predictions:
 * - On the first GET request, the browser should show a blank product contact form.
 * - On an invalid POST request, the same page should show validation errors and keep safe sticky values.
 * - On a valid POST request, the page should thank the visitor by name and repeat the sanitized topic and email.
 *
 * Expected $_POST contents after submission:
 * [
 *     'full_name' => 'Jordan',
 *     'email' => 'jordan@example.com',
 *     'topic' => 'Labrador training tips',
 *     'message' => 'A message between 50 and 150 words...',
 *     'submit' => 'Send Message',
 * ]
 *
 * Post-test reflections:
 * - POST keeps the form data out of the URL, which is better for contact-style messages than GET.
 * - htmlspecialchars() is essential when redisplaying user input because form fields can contain HTML-like text.
 * - The message word-count rule needs server-side validation because browser-only rules can be bypassed.
 * - Using $_SERVER['REQUEST_METHOD'] is clearer than relying only on whether a submit button exists.
 */

$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

$fullName = '';
$email = '';
$topic = '';
$message = '';
$wordCount = 0;

// escapeOutput() centralizes output sanitization so all user-controlled values
// are escaped consistently before being printed into HTML.
function escapeOutput(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// countMessageWords() validates the assignment's 50-150 word textarea rule.
// preg_split() handles repeated spaces and line breaks more reliably than explode().
function countMessageWords(string $value): int
{
    $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);

    return $words === false ? 0 : count($words);
}

if ($submitted) {
    // Superglobals are read with null coalescing so missing fields do not trigger notices.
    // trim() removes accidental whitespace before validation.
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $topic = trim((string) ($_POST['topic'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $wordCount = countMessageWords($message);

    if ($fullName === '') {
        $errors['full_name'] = 'Full name is required.';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($topic === '') {
        $errors['topic'] = 'Topic of message is required.';
    }

    if ($message === '') {
        $errors['message'] = 'Message is required.';
    } elseif ($wordCount < 50 || $wordCount > 150) {
        $errors['message'] = 'Message must be between 50 and 150 words. Current word count: '.$wordCount.'.';
    }
}

$isSuccessful = $submitted && $errors === [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Product Contact Form</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #111827;
            --muted: #5b6575;
            --panel: #ffffff;
            --line: #d8dee8;
            --accent: #0f766e;
            --accent-dark: #134e4a;
            --soft: #ecfeff;
            --danger-bg: #fee2e2;
            --danger-line: #fca5a5;
            --danger-text: #991b1b;
            --success-bg: #dcfce7;
            --success-line: #86efac;
            --success-text: #166534;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at 12% 0%, rgba(249, 115, 22, 0.18), transparent 28rem),
                radial-gradient(circle at 88% 8%, rgba(15, 118, 110, 0.2), transparent 30rem),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 52%, #ecfeff 100%);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 24px 70px rgba(17, 24, 39, 0.14);
            margin: 0 auto;
            max-width: 58rem;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #111827 0%, #134e4a 64%, #9a3412 100%);
            color: #ffffff;
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }

        .back-link {
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 999px;
            color: #ffffff;
            display: inline-flex;
            font-size: 0.9rem;
            font-weight: 700;
            justify-self: start;
            padding: 0.55rem 0.85rem;
            text-decoration: none;
        }

        .kicker {
            color: #fed7aa;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            margin: 0;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 3.6rem);
            line-height: 0.98;
            margin: 0;
            max-width: 46rem;
        }

        .intro {
            color: #d1fae5;
            margin: 0;
            max-width: 42rem;
        }

        .content {
            display: grid;
            gap: 1.25rem;
            padding: 1.5rem;
        }

        .summary {
            background: var(--soft);
            border: 1px solid #99f6e4;
            border-radius: 10px;
            color: var(--accent-dark);
            margin: 0;
            padding: 0.9rem 1rem;
        }

        .notice {
            border-radius: 10px;
            font-weight: 700;
            padding: 0.9rem 1rem;
        }

        .notice.error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-line);
            color: var(--danger-text);
        }

        .notice.success {
            background: var(--success-bg);
            border: 1px solid var(--success-line);
            color: var(--success-text);
        }

        .notice ul {
            margin: 0.5rem 0 0;
            padding-left: 1.25rem;
        }

        form {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }

        label {
            display: grid;
            font-weight: 800;
            gap: 0.4rem;
        }

        input,
        textarea {
            border: 1px solid #aab4c4;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            padding: 0.72rem;
        }

        input:focus,
        textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.16);
            outline: none;
        }

        textarea {
            min-height: 11rem;
            resize: vertical;
        }

        .hint {
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 400;
        }

        .field-error {
            color: var(--danger-text);
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .button {
            background: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font-weight: 800;
            padding: 0.75rem 1rem;
        }

        .button-secondary {
            background: #ffffff;
            border: 1px solid #aab4c4;
            border-radius: 8px;
            color: var(--ink);
            cursor: pointer;
            font-weight: 800;
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>
<main>
    <header class="page-header">
        <a class="back-link" href="/roadmap/module-3">Back to Module 3</a>
        <p class="kicker">Module 3 Assignment 3B</p>
        <h1>Secure Product Contact Form</h1>
        <p class="intro">
            A self-processing PHP form that uses POST, validates required fields, protects output with
            htmlspecialchars(), and confirms safe product inquiry submissions.
        </p>
    </header>

    <section class="content">
        <p class="summary">
            Message requirement: write 50-150 words. The form keeps your entries after validation errors and
            only shows submitted data after escaping it for safe HTML output.
        </p>

        <?php if ($submitted && $errors !== []) { ?>
            <div class="notice error">
                Please fix the following:
                <ul>
                    <?php // Escape each validation message before rendering it into the HTML page.?>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo escapeOutput($error); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php if ($isSuccessful) { ?>
            <div class="notice success">
                <p>
                    <?php // User-submitted values are escaped before display to help prevent XSS.?>
                    Thank you, <?php echo escapeOutput($fullName); ?>! We received your message about:
                    "<?php echo escapeOutput($topic); ?>"
                </p>
                <p>We'll get back to you at <?php echo escapeOutput($email); ?>.</p>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <label for="full_name">
                Full Name
                <?php // Sticky value: keep the submitted full name after a validation error.?>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?php echo escapeOutput($fullName); ?>"
                    required
                >
                <?php if (isset($errors['full_name'])) { ?>
                    <p class="field-error"><?php echo escapeOutput($errors['full_name']); ?></p>
                <?php } ?>
            </label>

            <label for="email">
                Email Address
                <?php // Sticky value: keep the submitted email address after a validation error.?>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo escapeOutput($email); ?>"
                    required
                >
                <?php if (isset($errors['email'])) { ?>
                    <p class="field-error"><?php echo escapeOutput($errors['email']); ?></p>
                <?php } ?>
            </label>

            <label for="topic">
                Topic of Message
                <?php // Sticky value: keep the submitted topic after a validation error.?>
                <input
                    type="text"
                    id="topic"
                    name="topic"
                    value="<?php echo escapeOutput($topic); ?>"
                    placeholder="Product setup, care tips, sizing, availability..."
                    required
                >
                <?php if (isset($errors['topic'])) { ?>
                    <p class="field-error"><?php echo escapeOutput($errors['topic']); ?></p>
                <?php } ?>
            </label>

            <label for="message">
                Message
                <span class="hint">Write 50-150 words. Current count after submit: <?php echo $wordCount; ?></span>
                <?php // Sticky value: keep the submitted message after a validation error.?>
                <textarea
                    id="message"
                    name="message"
                    required
                ><?php echo escapeOutput($message); ?></textarea>
                <?php if (isset($errors['message'])) { ?>
                    <p class="field-error"><?php echo escapeOutput($errors['message']); ?></p>
                <?php } ?>
            </label>

            <div class="actions">
                <input class="button" type="submit" name="submit" value="Send Message">
                <input class="button-secondary" type="reset" value="Clear Form">
            </div>
        </form>
    </section>
</main>
</body>
</html>
