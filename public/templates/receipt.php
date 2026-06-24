<?php

declare(strict_types=1);

use Cs85\Module2A\Presentation\ReceiptViewModel;

/** @var ReceiptViewModel $viewModel */
/** @var string $pageTitle */
/** @var string $eyebrow */
/** @var bool $hasSubmittedOrder */
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$orderSummary = $viewModel->orderSummary();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($pageTitle); ?></title>
    <style>
        :root {
            --ink: #17202a;
            --graphite: #334155;
            --teal: #14b8a6;
            --gold: #f5b841;
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
            width: min(100%, 720px);
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

        .home-link,
        button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            font: inherit;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .home-link {
            padding: 0 16px;
            color: var(--ink);
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

        .input-panel {
            padding: 24px 28px 28px;
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
        }

        .input-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        label {
            display: grid;
            gap: 7px;
            color: var(--graphite);
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        select,
        input[type="text"] {
            width: 100%;
            min-height: 44px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            background: var(--paper);
        }

        .checkbox-row {
            min-height: 44px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--paper);
            text-transform: none;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #0f766e;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        button {
            padding: 0 20px;
            color: var(--paper);
            background: #0f766e;
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

        .discount span:last-child {
            color: #0f766e;
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

        .empty-state {
            margin: 0;
            padding: 20px;
            border: 1px solid rgba(20, 184, 166, 0.28);
            border-radius: 8px;
            color: var(--graphite);
            background: #f0fdfa;
            line-height: 1.55;
        }

        @media (max-width: 560px) {
            body {
                padding: 18px;
            }

            .receipt-header,
            .receipt-body,
            .meta,
            .input-panel {
                padding-left: 20px;
                padding-right: 20px;
            }

            .meta,
            .input-grid {
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
        <p class="eyebrow"><?= $escape($eyebrow); ?></p>
        <h1>Order Summary</h1>
    </div>

    <form class="input-panel" method="get">
        <input type="hidden" name="calculate" value="1">

        <div class="input-grid" aria-label="Order inputs">
            <label>
                Size
                <select name="size">
                    <?php foreach (['S', 'M', 'L', 'XL'] as $sizeOption) { ?>
                        <option value="<?= $escape($sizeOption); ?>" <?= $orderSummary['size'] === $sizeOption ? 'selected' : ''; ?>>
                            <?= $escape($sizeOption); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>

            <label>
                Color
                <select name="color">
                    <?php foreach (['Black', 'White', 'Sunset Orange', 'Ocean Blue'] as $colorOption) { ?>
                        <option value="<?= $escape($colorOption); ?>" <?= $orderSummary['color'] === $colorOption ? 'selected' : ''; ?>>
                            <?= $escape($colorOption); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>

            <label>
                First Name
                <input type="text" name="customer_first_name" value="<?= $escape($orderSummary['customer_first_name']); ?>">
            </label>

            <label>
                Custom Text
                <span class="checkbox-row">
                    <input type="checkbox" name="is_customized" value="1" <?= $orderSummary['customized'] === 'Yes' ? 'checked' : ''; ?>>
                    Add customization
                </span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit">Calculate Price</button>
        </div>
    </form>

    <?php if (! $hasSubmittedOrder) { ?>
        <div class="receipt-body">
            <p class="empty-state">Choose the order details, then calculate the price.</p>
        </div>
    <?php } else { ?>

    <section class="meta" aria-label="Order configuration">
        <div>
            <span>Size</span>
            <strong><?= $escape($orderSummary['size']); ?></strong>
        </div>
        <div>
            <span>Color</span>
            <strong><?= $escape($orderSummary['color']); ?></strong>
        </div>
        <div>
            <span>Custom</span>
            <strong><?= $escape($orderSummary['customized']); ?></strong>
        </div>
        <div>
            <span>First Name</span>
            <strong><?= $escape($orderSummary['customer_first_name']); ?></strong>
        </div>
    </section>

    <div class="receipt-body">
        <ul>
            <?php foreach ($viewModel->lineItems() as $lineItem) { ?>
                <li class="<?= $lineItem['is_discount'] ? 'discount' : ''; ?>">
                    <span><?= $escape($lineItem['label']); ?></span>
                    <span><?= $escape($lineItem['amount']); ?></span>
                </li>
            <?php } ?>
        </ul>

        <ul class="total-row">
            <li>
                <span class="total">Final Price</span>
                <span class="total"><?= $escape($viewModel->total()); ?></span>
            </li>
        </ul>
    </div>
    <?php } ?>
</div>
</body>
</html>
