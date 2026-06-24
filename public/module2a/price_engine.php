<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T-Shirt Price Engine</title>
    <style>
        :root {
            --ink: #17202a;
            --graphite: #334155;
            --sky: #0ea5e9;
            --teal: #14b8a6;
            --gold: #f5b841;
            --coral: #f9736b;
            --success: #16a34a;
            --paper: #ffffff;
            --line: rgba(23, 32, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #eef7fb 0%, #f8fbf7 48%, #fff5ee 100%);
        }

        .receipt {
            width: min(100%, 520px);
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 60px rgba(23, 32, 42, 0.14);
        }

        .receipt-header {
            padding: 28px 28px 22px;
            color: var(--paper);
            background: linear-gradient(135deg, var(--ink), #0f766e);
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 22px;
        }

        .home-link {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            border-radius: 8px;
            color: var(--ink);
            font-weight: 800;
            text-decoration: none;
            background: var(--gold);
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--gold);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 7vw, 3.2rem);
            line-height: 1.04;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            padding: 18px 28px;
            background: #f8fafc;
            border-bottom: 1px solid var(--line);
        }

        .meta div {
            min-width: 0;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--paper);
        }

        .meta span {
            display: block;
            color: var(--graphite);
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta strong {
            display: block;
            margin-top: 4px;
            overflow-wrap: anywhere;
            font-size: 0.98rem;
        }

        .receipt-body {
            padding: 24px 28px 28px;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        li {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid rgba(23, 32, 42, 0.1);
            color: var(--graphite);
        }

        li span:last-child {
            color: var(--ink);
            font-weight: 800;
            white-space: nowrap;
        }

        .total-row {
            margin-top: 18px;
            padding: 18px;
            border: 1px solid rgba(22, 163, 74, 0.28);
            border-radius: 8px;
            background: #f0fdf4;
        }

        .total-row li {
            padding: 0;
            border: 0;
        }

        .total {
            color: var(--success);
            font-size: 1.4rem;
            font-weight: 900;
        }

        @media (max-width: 560px) {
            body {
                padding: 18px;
            }

            .receipt-header,
            .receipt-body,
            .meta {
                padding-left: 20px;
                padding-right: 20px;
            }

            .meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <div class="top-actions">
            <a class="home-link" href="/roadmap/module-2">Back to Module 2</a>
        </div>
        <p class="eyebrow">Part A - first draft</p>
        <h1>Order Summary</h1>
    </div>

    <?php
        // --- Configuration: Change these values to test all business rules. ---
        $size = 'XL'; // Options: 'S', 'M', 'L', 'XL'
    $color = 'Sunset Orange'; // Test with 'Sunset Orange' or 'Ocean Blue' for premium color pricing.
    $isCustomized = true; // Options: true, false
    $customerFirstName = 'Sergio'; // Replace with your actual first name.

    // --- Part A: Implemented with simple if statements only. ---
    $finalPrice = 22.50;
    $details = '<li><span>Base Price</span><span>$'.number_format($finalPrice, 2).'</span></li>';

    if ($size == 'L') {
        $finalPrice = $finalPrice + 1.75;
        $details = $details.'<li><span>Size L Upcharge</span><span>+$1.75</span></li>';
    }

    if ($size == 'XL') {
        $finalPrice = $finalPrice + 2.50;
        $details = $details.'<li><span>Size XL Upcharge</span><span>+$2.50</span></li>';
    }

    if ($color == 'Sunset Orange') {
        $finalPrice = $finalPrice + 2.00;
        $details = $details.'<li><span>Premium Color: Sunset Orange</span><span>+$2.00</span></li>';
    }

    if ($color == 'Ocean Blue') {
        $finalPrice = $finalPrice + 2.00;
        $details = $details.'<li><span>Premium Color: Ocean Blue</span><span>+$2.00</span></li>';
    }

    if ($isCustomized == true) {
        $finalPrice = $finalPrice + 5.00;
        $details = $details.'<li><span>Custom Text Fee</span><span>+$5.00</span></li>';

        if ($size == 'XL') {
            $finalPrice = $finalPrice + 3.00;
            $details = $details.'<li><span>XL Custom Handling Fee</span><span>+$3.00</span></li>';
        }
    }

    if (strlen($customerFirstName) > 6) {
        $finalPrice = $finalPrice - 1.00;
        $details = $details.'<li><span>Long Name Discount</span><span>-$1.00</span></li>';
    }

    $customizationLabel = 'No';

    if ($isCustomized == true) {
        $customizationLabel = 'Yes';
    }

    // --- DO NOT EDIT BELOW THIS LINE ---
    echo "<section class='meta' aria-label='Order configuration'>";
    echo '<div><span>Size</span><strong>'.htmlspecialchars($size).'</strong></div>';
    echo '<div><span>Color</span><strong>'.htmlspecialchars($color).'</strong></div>';
    echo '<div><span>Custom</span><strong>'.$customizationLabel.'</strong></div>';
    echo '</section>';
    echo "<div class='receipt-body'>";
    echo '<ul>'.$details.'</ul>';
    echo "<ul class='total-row'><li><span class='total'>Final Price</span><span class='total'>$".number_format($finalPrice, 2).'</span></li></ul>';
    echo '</div>';
    ?>
</div>
</body>
</html>
