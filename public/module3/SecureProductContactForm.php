<?php
/*
 * Module 3 Assignment 3B: Secure Product Contact Form
 * Student: Serge Hall
 * GitHub Repository: cs85-module3b-createform
 */

$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

$fullName = '';
$email = '';
$topic = '';
$message = '';
$wordCount = 0;

function escapeOutput(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function countMessageWords(string $value): int
{
    $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);

    return $words === false ? 0 : count($words);
}

if ($submitted) {
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
        $errors['message'] = 'Message must be between 50 and 150 words. Current word count: ' . $wordCount . '.';
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
        body {
            background: #f8fafc;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 2rem 1rem;
        }

        main {
            background: #ffffff;
            border: 1px solid #d8dee8;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 48rem;
            padding: 1.5rem;
        }

        label {
            display: grid;
            font-weight: 700;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }

        input,
        textarea {
            border: 1px solid #aab4c4;
            border-radius: 6px;
            font: inherit;
            padding: 0.7rem;
        }

        textarea {
            min-height: 10rem;
            resize: vertical;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
    </style>
</head>
<body>
<main>
    <a href="/roadmap/module-3">Back to Module 3</a>
    <h1>Secure Product Contact Form</h1>
    <p>Use this form to ask a question about a product or product-related topic.</p>

    <?php if ($submitted && $errors !== []) { ?>
        <section>
            <h2>Please fix the following:</h2>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo escapeOutput($error); ?></li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>

    <?php if ($isSuccessful) { ?>
        <section>
            <p>
                Thank you, <?php echo escapeOutput($fullName); ?>! We received your message about:
                "<?php echo escapeOutput($topic); ?>"
            </p>
            <p>We'll get back to you at <?php echo escapeOutput($email); ?>.</p>
        </section>
    <?php } ?>

    <form action="" method="POST">
        <label for="full_name">
            Full Name
            <input type="text" id="full_name" name="full_name" value="<?php echo escapeOutput($fullName); ?>" required>
            <?php if (isset($errors['full_name'])) { ?>
                <p><?php echo escapeOutput($errors['full_name']); ?></p>
            <?php } ?>
        </label>

        <label for="email">
            Email Address
            <input type="email" id="email" name="email" value="<?php echo escapeOutput($email); ?>" required>
            <?php if (isset($errors['email'])) { ?>
                <p><?php echo escapeOutput($errors['email']); ?></p>
            <?php } ?>
        </label>

        <label for="topic">
            Topic of Message
            <input type="text" id="topic" name="topic" value="<?php echo escapeOutput($topic); ?>" required>
            <?php if (isset($errors['topic'])) { ?>
                <p><?php echo escapeOutput($errors['topic']); ?></p>
            <?php } ?>
        </label>

        <label for="message">
            Message
            <span>Write 50-150 words. Current count after submit: <?php echo $wordCount; ?></span>
            <textarea id="message" name="message" required><?php echo escapeOutput($message); ?></textarea>
            <?php if (isset($errors['message'])) { ?>
                <p><?php echo escapeOutput($errors['message']); ?></p>
            <?php } ?>
        </label>

        <div class="actions">
            <input type="submit" name="submit" value="Send Message">
            <input type="reset" value="Clear Form">
        </div>
    </form>
</main>
</body>
</html>
