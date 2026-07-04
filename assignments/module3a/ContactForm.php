<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Me</title>
    <style>
        :root {
            color-scheme: light;
            --accent: #0369a1;
            --accent-dark: #0f172a;
            --accent-soft: #e0f2fe;
            --border: #cbd5e1;
            --muted: #64748b;
            --surface: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 34rem),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 48%, #e0f2fe 100%);
            color: var(--accent-dark);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            margin: 0 auto;
            max-width: 48rem;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
            color: #ffffff;
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }

        .top-link {
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

        .page-kicker {
            color: #bae6fd;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            margin: 0;
            text-transform: uppercase;
        }

        .page-title {
            font-size: clamp(2rem, 6vw, 3.5rem);
            line-height: 0.95;
            margin: 0;
        }

        .page-copy {
            color: #dbeafe;
            margin: 0;
            max-width: 38rem;
        }

        .form-panel {
            padding: 1.5rem;
        }

        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }

        .form-title {
            margin: 0;
            text-align: center;
        }

        label {
            display: grid;
            font-weight: 700;
            gap: 0.35rem;
            margin-bottom: 1rem;
        }

        input,
        textarea {
            border: 1px solid #94a3b8;
            border-radius: 6px;
            font: inherit;
            padding: 0.65rem;
        }

        textarea {
            min-height: 9rem;
            resize: vertical;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .actions input {
            cursor: pointer;
            font-weight: 700;
        }

        .button {
            background: var(--accent);
            border-color: var(--accent);
            color: #ffffff;
        }

        .button-secondary {
            background: #ffffff;
            color: var(--accent-dark);
        }

        .form-alert {
            border-radius: 8px;
            font-weight: 700;
            margin: 0 0 0.75rem;
            padding: 0.75rem 0.9rem;
        }

        .form-alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .form-alert.info {
            background: var(--accent-soft);
            border: 1px solid #7dd3fc;
            color: #075985;
        }

        .form-alert.success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

    </style>
</head>
<body>
<main>
    <header class="page-header">
        <a class="top-link" href="/roadmap/module-3">Back to Module 3</a>
        <p class="page-kicker">Module 3 Assignment 3A</p>
        <h1 class="page-title">Review and Comment on Form</h1>
        <p class="page-copy">
            This page keeps the course Contact Me script in one PHP file, then documents how validation,
            sticky form values, and message handling work.
        </p>
    </header>
    <section class="form-panel">
<?php
/*
 * Module 3 Assignment 3A: Review and Comment on Form
 *
 * This file follows the provided Contact Me form-handling script and adds
 * detailed comments explaining the validation, sticky form behavior, mail
 * logic, and final reflection required by the assignment.
 */

// validateInput() handles required text fields such as name, subject, and message.
// It receives the submitted value and a readable field name used in error output.
if (! function_exists('validateInput')) {
    function validateInput($data, $fieldName)
    {
        // The function updates the shared counter so the main script can decide
        // whether the form has validation errors after all fields are checked.
        global $errorCount;

        // empty() catches missing values, empty strings, and other empty-like input.
        // If the field is empty, the script prints an error and returns an empty value.
        if (empty($data)) {
            echo "<p class=\"form-alert error\">\"$fieldName\" is a required field.</p>\n";
            $errorCount++;
            $retval = '';
        } else {
            // Only clean up the input if it is not empty.
            // trim() removes extra spaces from the beginning and end of the value.
            $retval = trim($data);

            // stripslashes() removes backslashes that may have been added before quotes.
            // This is older PHP-form cleanup logic, but it helps explain the data flow.
            $retval = stripslashes($retval);
        }

        // The cleaned value is returned so it can be reused in the sticky form or email.
        return $retval;
    }
}

// validateEmail() is similar to validateInput(), but it adds e-mail-specific checks.
// It confirms the field is not empty, sanitizes the address, then validates its format.
if (! function_exists('validateEmail')) {
    function validateEmail($data, $fieldName)
    {
        // This shared counter lets the main script know if any validation step failed.
        global $errorCount;

        // An empty e-mail cannot be used as a sender address, so it is a required field.
        if (empty($data)) {
            echo "<p class=\"form-alert error\">\"$fieldName\" is a required field.</p>\n";
            $errorCount++;
            $retval = '';
        } else {
            // FILTER_SANITIZE_EMAIL removes characters that do not belong in e-mail input.
            $retval = filter_var($data, FILTER_SANITIZE_EMAIL);

            // FILTER_VALIDATE_EMAIL checks whether the sanitized value looks like a real
            // e-mail address. Invalid format should count as a validation error too.
            if (! filter_var($retval, FILTER_VALIDATE_EMAIL)) {
                echo "<p class=\"form-alert error\">\"$fieldName\" is not a valid e-mail address.</p>\n";
                $errorCount++;
            }
        }

        // The sanitized e-mail value is returned for sticky display and mail headers.
        return $retval;
    }
}

// displayForm() renders the HTML form and receives one value for each field.
// Passing the current values back into the form creates "sticky" form behavior:
// if validation fails, the user does not have to retype every field.
if (! function_exists('displayForm')) {
    function displayForm($Sender, $Email, $Subject, $Message)
    {
        // htmlspecialchars() safely displays sticky user values inside HTML.
        // ENT_QUOTES also protects attribute values that use quotes.
        $safeSender = htmlspecialchars($Sender, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($Email, ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars($Subject, ENT_QUOTES, 'UTF-8');
        $safeMessage = htmlspecialchars($Message, ENT_QUOTES, 'UTF-8');
        ?>
    <div class="form-card">
    <h2 class="form-title">Contact Me</h2>
    <form name="contact" action="" method="post">
        <?php echo csrf_field(); ?>
        <p>
            <label>
                Your Name:
                <!-- The value attribute reuses the submitted name after a failed validation attempt. -->
                <input type="text" name="Sender" value="<?php echo $safeSender; ?>" />
            </label>
        </p>
        <p>
            <label>
                Your E-mail:
                <!-- The e-mail field is sticky too, but it is validated by validateEmail(). -->
                <input type="text" name="Email" value="<?php echo $safeEmail; ?>" />
            </label>
        </p>
        <p>
            <label>
                Subject:
                <!-- Subject is handled as regular text input by validateInput(). -->
                <input type="text" name="Subject" value="<?php echo $safeSubject; ?>" />
            </label>
        </p>
        <p>
            <label>
                Message:<br />
                <!-- Textarea content is placed between the opening and closing tags. -->
                <textarea name="Message"><?php echo $safeMessage; ?></textarea>
            </label>
        </p>
        <p class="actions">
            <!-- Reset clears the visible browser form fields without submitting the form. -->
            <input class="button-secondary" type="reset" value="Clear Form" />&nbsp; &nbsp;

            <!-- This button creates $_POST['Submit'], which the PHP script checks below. -->
            <input class="button" type="submit" name="Submit" value="Send Form" />
        </p>
    </form>
    </div>
    <?php
    }
}

// These variables define the starting state before any form submission happens.
// The form should appear on first page load, so $ShowForm starts as TRUE.
$ShowForm = true;
$errorCount = 0;
$Sender = '';
$Email = '';
$Subject = '';
$Message = '';

// isset($_POST['Submit']) checks whether the submit button was pressed.
// On the first page load there is no POST data, so this block is skipped.
if (isset($_POST['Submit'])) {
    // Each submitted field is validated and then stored in a variable.
    // These variables are later used either for sticky display or for the email.
    $Sender = validateInput($_POST['Sender'], 'Your Name');
    $Email = validateEmail($_POST['Email'], 'Your E-mail');
    $Subject = validateInput($_POST['Subject'], 'Subject');
    $Message = validateInput($_POST['Message'], 'Message');

    // If no required-field errors were counted, the form can be hidden and the
    // script can try to send the email. Otherwise, the form stays visible.
    if ($errorCount == 0) {
        $ShowForm = false;
    } else {
        $ShowForm = true;
    }
}

// This conditional decides whether to show the form or process a successful submission.
if ($ShowForm == true) {
    // If there were errors, this message appears above the sticky form.
    if ($errorCount > 0) {
        echo "<p class=\"form-alert info\">Please re-enter the form information below.</p>\n";
    }

    // The form is displayed with the current field values.
    displayForm($Sender, $Email, $Subject, $Message);
} else {
    // The sender address combines the user's name and email for the From header.
    $SenderAddress = "$Sender <$Email>";

    // The CC header sends a copy of the form message back to the sender.
    $Headers = "From: $SenderAddress\nCC: $SenderAddress\n";

    // mail() tries to send the message to the site owner.
    // recipient@example.com is a placeholder address from the assignment.
    $result = mail('recipient@example.com', $Subject, $Message, $Headers);

    // The script prints a simple success or failure message based on mail().
    if ($result) {
        echo '<p class="form-alert success">Your message has been sent. Thank you, '.htmlspecialchars($Sender, ENT_QUOTES, 'UTF-8').".</p>\n";
    } else {
        echo '<p class="form-alert error">There was an error sending your message, '.htmlspecialchars($Sender, ENT_QUOTES, 'UTF-8').".</p>\n";
    }
}

/*
 * Reflection
 *
 * What does each function do?
 * validateInput() checks required text fields, counts errors, trims whitespace,
 * removes backslashes, and returns the cleaned value. validateEmail() checks the
 * required e-mail field, sanitizes it with FILTER_SANITIZE_EMAIL, validates the
 * format with FILTER_VALIDATE_EMAIL, and returns the sanitized address.
 * displayForm() prints the contact form and places the current variable values
 * back into the form fields so the form is sticky after validation errors.
 *
 * How is user input protected?
 * The script protects input by requiring all fields, trimming extra whitespace,
 * removing slashes, sanitizing the e-mail address, validating the e-mail format,
 * and escaping sticky values with htmlspecialchars() before showing them in
 * HTML. This is a good beginner introduction to input handling, but a real app
 * should also add CSRF protection and stronger mail header validation.
 *
 * What were the most confusing parts?
 * The most confusing part was the way the PHP code moves in and out of HTML
 * inside displayForm(). The global $errorCount variable also requires careful
 * attention because functions update it indirectly instead of returning a full
 * validation result.
 *
 * What could be improved?
 * The form could use a real configured recipient, validate mail headers more
 * strictly, and use CSRF protection. In Laravel, this would usually become a
 * route, controller, Blade view, form request validation class, and mail class
 * instead of one large PHP file.
 *
 * Why send a copy of the form to the sender?
 * Sending a copy gives the sender a receipt of what they submitted. It helps
 * confirm that the message was sent, lets the sender review their own wording,
 * and creates a useful record if they need to follow up later.
 */
?>
    </section>
</main>
</body>
</html>
