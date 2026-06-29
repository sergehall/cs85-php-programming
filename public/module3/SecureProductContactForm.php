<?php
/*
 * Module 3 Assignment 3B: Secure Product Contact Form
 * Student: Serge Hall
 * GitHub Repository: cs85-module3b-createform
 */
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

    <form action="" method="POST">
        <label for="full_name">
            Full Name
            <input type="text" id="full_name" name="full_name" required>
        </label>

        <label for="email">
            Email Address
            <input type="email" id="email" name="email" required>
        </label>

        <label for="topic">
            Topic of Message
            <input type="text" id="topic" name="topic" required>
        </label>

        <label for="message">
            Message
            <textarea id="message" name="message" required></textarea>
        </label>

        <div class="actions">
            <input type="submit" name="submit" value="Send Message">
            <input type="reset" value="Clear Form">
        </div>
    </form>
</main>
</body>
</html>
