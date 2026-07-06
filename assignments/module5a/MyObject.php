<?php
/*
 * Module 5 Assignment 5A: Designing Your Own Object Oriented World
 * Student: Serge Hall
 * Topic: SERGIOARTG photography booking and pricing workflow
 * Local project path: assignments/module5a/
 *
 * Output predictions before running:
 * - The constructor should create each PhotographyBooking object with real
 *   SERGIOARTG-style booking data: photographer, brand, client, service type,
 *   package, hours, edited photo count, date, location, status, deposit, rush
 *   delivery flag, and notes.
 * - getSummary() should return a readable booking summary for the selected
 *   client and shoot type.
 * - calculateTotalCents() should return the full quote total using package
 *   price, extra hours, extra retouching, location fee, rush fee, and service
 *   multiplier.
 * - calculateDepositDueCents() should return the required deposit minus any
 *   deposit already paid, never below zero.
 * - addRetouchPhotos() should change the retouched photo count property.
 * - changeStatus() should change the booking status property when the selected
 *   status is allowed.
 * - getBookingDecision() should return a decision based on status, deposit,
 *   quote total, preferred date, and booking complexity.
 * - getPriorityLabel() was generated with AI first, then corrected and styled.
 */

declare(strict_types=1);

if (! class_exists('PhotographyBooking', false)) {
    class PhotographyBooking
    {
        public string $photographerName;

        public string $brandName;

        public string $clientName;

        public string $clientEmail;

        public string $serviceType;

        public string $packageName;

        public int $basePriceCents;

        public float $durationHours;

        public int $includedRetouchedPhotos;

        public int $retouchedPhotos;

        public string $shootDate;

        public string $locationType;

        public string $status;

        public int $depositPaidCents;

        public bool $rushDelivery;

        public string $notes;

        public function __construct(
            string $photographerName,
            string $brandName,
            string $clientName,
            string $clientEmail,
            string $serviceType,
            string $packageName,
            int $basePriceCents,
            float $durationHours,
            int $includedRetouchedPhotos,
            int $retouchedPhotos,
            string $shootDate,
            string $locationType,
            string $status,
            int $depositPaidCents,
            bool $rushDelivery,
            string $notes
        ) {
            $this->photographerName = $photographerName;
            $this->brandName = $brandName;
            $this->clientName = $clientName;
            $this->clientEmail = $clientEmail;
            $this->serviceType = $serviceType;
            $this->packageName = $packageName;
            $this->basePriceCents = max(0, $basePriceCents);
            $this->durationHours = max(1, $durationHours);
            $this->includedRetouchedPhotos = max(0, $includedRetouchedPhotos);
            $this->retouchedPhotos = max($this->includedRetouchedPhotos, $retouchedPhotos);
            $this->shootDate = $shootDate;
            $this->locationType = $locationType;
            $this->status = $status;
            $this->depositPaidCents = max(0, $depositPaidCents);
            $this->rushDelivery = $rushDelivery;
            $this->notes = $notes;
        }

        public function getSummary(): string
        {
            return sprintf(
                '%s for %s: %s %s package on %s at %s. %.1f hours, %d edited photos, status %s. Notes: %s',
                $this->brandName,
                $this->clientName,
                $this->serviceType,
                $this->packageName,
                $this->formatShootDate(),
                $this->formatLocationType(),
                $this->durationHours,
                $this->retouchedPhotos,
                $this->status,
                $this->notes
            );
        }

        public function calculateTotalCents(): int
        {
            $includedHours = $this->getIncludedHours();
            $extraHours = max(0, $this->durationHours - $includedHours);
            $extraRetouchCount = max(0, $this->retouchedPhotos - $this->includedRetouchedPhotos);

            $subtotal = $this->basePriceCents;
            $subtotal += (int) round($extraHours * 12000);
            $subtotal += $extraRetouchCount * 500;
            $subtotal += $this->getLocationFeeCents();
            $subtotal += $this->rushDelivery ? 4500 : 0;

            return (int) round($subtotal * $this->getServiceMultiplier());
        }

        public function calculateDepositDueCents(): int
        {
            $requiredDeposit = (int) round($this->calculateTotalCents() * 0.3);

            return max(0, $requiredDeposit - $this->depositPaidCents);
        }

        public function addRetouchPhotos(int $photoCount): void
        {
            if ($photoCount <= 0) {
                return;
            }

            $this->retouchedPhotos += $photoCount;
            $this->notes = 'Client requested '.$photoCount.' extra edited photos.';
        }

        public function changeStatus(string $status): void
        {
            $allowedStatuses = ['new', 'quoted', 'deposit_paid', 'confirmed', 'completed'];

            if (in_array($status, $allowedStatuses, true)) {
                $this->status = $status;
            }
        }

        public function recordDepositPayment(int $amountCents): void
        {
            if ($amountCents <= 0) {
                return;
            }

            $this->depositPaidCents = min($this->calculateTotalCents(), $this->depositPaidCents + $amountCents);

            if ($this->calculateDepositDueCents() === 0 && $this->status !== 'completed') {
                $this->status = 'deposit_paid';
            }
        }

        public function getBookingDecision(): string
        {
            if ($this->status === 'completed') {
                return 'Archive this order and request a client review.';
            }

            if ($this->calculateDepositDueCents() > 0) {
                return 'Send quote and collect the 30% deposit before confirming the shoot.';
            }

            if ($this->isHighComplexity()) {
                return 'Deposit is covered, but review logistics before final confirmation.';
            }

            if ($this->daysUntilShoot() <= 2) {
                return 'Deposit is covered and the shoot is soon. Confirm schedule and final details today.';
            }

            return 'Ready to confirm the booking and prepare the client timeline.';
        }

        public function getPriorityLabel(): string
        {
            if ($this->status === 'completed') {
                return 'Done';
            }

            if ($this->daysUntilShoot() <= 2 || $this->rushDelivery) {
                return 'High priority';
            }

            if ($this->calculateDepositDueCents() > 0) {
                return 'Waiting for deposit';
            }

            return 'Normal priority';
        }

        public function getIncludedHours(): float
        {
            return match ($this->packageName) {
                'Mini' => 1.0,
                'Premium' => 4.0,
                default => 2.0,
            };
        }

        public function getMethodTrace(string $methodName): string
        {
            return 'Method called: '.$methodName.'() on '.$this->clientName.' booking.';
        }

        private function getLocationFeeCents(): int
        {
            return match ($this->locationType) {
                'studio' => 5000,
                'client_place' => 3500,
                'out_of_city' => 12000,
                default => 0,
            };
        }

        private function getServiceMultiplier(): float
        {
            return match ($this->serviceType) {
                'Fashion', 'Love story', 'Family', 'Content' => 1.15,
                'Commercial' => 1.25,
                default => 1.0,
            };
        }

        private function isHighComplexity(): bool
        {
            return $this->locationType === 'out_of_city'
                || $this->serviceType === 'Commercial'
                || $this->durationHours >= 4
                || $this->retouchedPhotos >= 30;
        }

        private function daysUntilShoot(): int
        {
            $today = new DateTimeImmutable('today');
            $shootDate = DateTimeImmutable::createFromFormat('Y-m-d', $this->shootDate) ?: $today;

            return (int) $today->diff($shootDate)->format('%r%a');
        }

        private function formatShootDate(): string
        {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $this->shootDate);

            return $date ? $date->format('M j, Y') : $this->shootDate;
        }

        private function formatLocationType(): string
        {
            return str_replace('_', ' ', $this->locationType);
        }
    }
}

if (! function_exists('h')) {
    function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('moneyFromCents')) {
    function moneyFromCents(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}

if (! function_exists('selected')) {
    function selected(string $actual, string $expected): string
    {
        return $actual === $expected ? ' selected' : '';
    }
}

$bookings = [
    'portrait' => new PhotographyBooking(
        'Serge Hall',
        'SERGIOARTG',
        'Maya Chen',
        'maya@example.com',
        'Portrait',
        'Standard',
        30000,
        2.0,
        20,
        20,
        '2026-07-10',
        'outdoor',
        'quoted',
        0,
        false,
        'Cinematic editorial portraits for a personal brand refresh.'
    ),
    'fashion' => new PhotographyBooking(
        'Serge Hall',
        'SERGIOARTG',
        'Northline Studio',
        'producer@example.com',
        'Fashion',
        'Premium',
        45000,
        4.0,
        30,
        30,
        '2026-07-08',
        'studio',
        'new',
        0,
        true,
        'Lookbook shoot with fast delivery and production coordination.'
    ),
];

$selectedBookingKey = (string) ($_POST['booking'] ?? 'portrait');
if (! isset($bookings[$selectedBookingKey])) {
    $selectedBookingKey = 'portrait';
}

$selectedBooking = $bookings[$selectedBookingKey];
$action = (string) ($_POST['action'] ?? 'summary');
$methodOutput = $selectedBooking->getSummary();
$methodTrace = $selectedBooking->getMethodTrace('getSummary');
$extraPhotoCount = filter_var($_POST['extra_photos'] ?? 5, FILTER_VALIDATE_INT);
$extraPhotoCount = is_int($extraPhotoCount) ? max(1, min($extraPhotoCount, 40)) : 5;
$depositAmountDollars = filter_var($_POST['deposit_amount'] ?? 150, FILTER_VALIDATE_FLOAT);
$depositAmountCents = is_float($depositAmountDollars) || is_int($depositAmountDollars)
    ? (int) round(max(1, min((float) $depositAmountDollars, 5000)) * 100)
    : 15000;
$newStatus = (string) ($_POST['new_status'] ?? 'confirmed');

$requestMethod = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($requestMethod === 'POST') {
    switch ($action) {
        case 'quote':
            $methodOutput = 'Calculated quote total: '.moneyFromCents($selectedBooking->calculateTotalCents())
                .'. Deposit still due: '.moneyFromCents($selectedBooking->calculateDepositDueCents()).'.';
            $methodTrace = $selectedBooking->getMethodTrace('calculateTotalCents and calculateDepositDueCents');
            break;

        case 'add_retouch':
            $selectedBooking->addRetouchPhotos($extraPhotoCount);
            $methodOutput = 'Updated retouch count to '.$selectedBooking->retouchedPhotos
                .' photos. New quote total: '.moneyFromCents($selectedBooking->calculateTotalCents()).'.';
            $methodTrace = $selectedBooking->getMethodTrace('addRetouchPhotos');
            break;

        case 'deposit':
            $selectedBooking->recordDepositPayment($depositAmountCents);
            $methodOutput = 'Recorded deposit payment of '.moneyFromCents($depositAmountCents)
                .'. Deposit still due: '.moneyFromCents($selectedBooking->calculateDepositDueCents())
                .'. Current status: '.$selectedBooking->status.'.';
            $methodTrace = $selectedBooking->getMethodTrace('recordDepositPayment');
            break;

        case 'status':
            $selectedBooking->changeStatus($newStatus);
            $methodOutput = 'Changed booking status to '.$selectedBooking->status.'.';
            $methodTrace = $selectedBooking->getMethodTrace('changeStatus');
            break;

        case 'decision':
            $methodOutput = $selectedBooking->getBookingDecision();
            $methodTrace = $selectedBooking->getMethodTrace('getBookingDecision');
            break;

        case 'priority':
            $methodOutput = 'Priority label: '.$selectedBooking->getPriorityLabel().'.';
            $methodTrace = $selectedBooking->getMethodTrace('getPriorityLabel');
            break;

        default:
            $methodOutput = $selectedBooking->getSummary();
            $methodTrace = $selectedBooking->getMethodTrace('getSummary');
            break;
    }
}

$totalPipelineCents = array_sum(array_map(
    static fn (PhotographyBooking $booking): int => $booking->calculateTotalCents(),
    $bookings
));
$depositDueCents = array_sum(array_map(
    static fn (PhotographyBooking $booking): int => $booking->calculateDepositDueCents(),
    $bookings
));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module 5A SERGIOARTG OOP Booking World</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #171514;
            --muted: #625d56;
            --panel: #ffffff;
            --line: #ded8cf;
            --accent: #0f766e;
            --accent-dark: #134e4a;
            --warm: #b45309;
            --soft: #f3f7f5;
            --photo-black: #121110;
            --success: #166534;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                linear-gradient(135deg, rgba(18, 17, 16, 0.86), rgba(19, 78, 74, 0.72)),
                url("https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1800&q=80") center/cover fixed;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 14px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.34);
            margin: 0 auto;
            max-width: 72rem;
            overflow: hidden;
        }

        .hero {
            background: linear-gradient(135deg, rgba(18, 17, 16, 0.96), rgba(19, 78, 74, 0.9), rgba(146, 64, 14, 0.84));
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
            font-size: clamp(2.1rem, 6vw, 4rem);
            line-height: 0.98;
            margin: 0;
            max-width: 52rem;
        }

        h2,
        h3 {
            margin: 0;
        }

        .intro {
            color: #d1fae5;
            margin: 0;
            max-width: 48rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .promo-link {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 0.92rem;
            font-weight: 800;
            min-height: 2.6rem;
            padding: 0.65rem 0.95rem;
            text-decoration: none;
        }

        .promo-link.primary {
            background: #ffffff;
            color: var(--photo-black);
        }

        .promo-link.secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.28);
            color: #ffffff;
        }

        .content {
            display: grid;
            gap: 1.25rem;
            padding: 1.5rem;
        }

        .metrics,
        .workspace,
        .booking-grid,
        .method-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 840px) {
            .metrics {
                grid-template-columns: repeat(3, 1fr);
            }

            .workspace {
                grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.25fr);
                align-items: start;
            }

            .booking-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .metric,
        .panel,
        .booking-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 1rem;
        }

        .metric {
            background: var(--soft);
        }

        .metric span {
            color: var(--muted);
            display: block;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .metric strong {
            color: var(--accent-dark);
            display: block;
            font-size: 2rem;
            line-height: 1.1;
            margin-top: 0.25rem;
        }

        .panel {
            display: grid;
            gap: 1rem;
        }

        .method-grid {
            grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr));
        }

        label {
            color: var(--muted);
            display: grid;
            font-size: 0.9rem;
            font-weight: 800;
            gap: 0.35rem;
        }

        select,
        input {
            border: 1px solid #b8b0a5;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            min-height: 2.7rem;
            padding: 0.65rem 0.75rem;
        }

        button {
            background: var(--photo-black);
            border: 1px solid var(--photo-black);
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            min-height: 2.8rem;
            padding: 0.7rem 0.9rem;
            text-align: center;
        }

        button.secondary {
            background: #ffffff;
            color: var(--accent-dark);
            border-color: var(--accent);
        }

        button.warm {
            background: var(--warm);
            border-color: var(--warm);
        }

        .result {
            background: #ecfdf5;
            border: 1px solid #86efac;
            border-radius: 12px;
            color: var(--success);
            display: grid;
            gap: 0.45rem;
            padding: 1rem;
        }

        .trace {
            color: var(--accent-dark);
            font-size: 0.9rem;
            font-weight: 800;
            margin: 0;
        }

        .output {
            margin: 0;
        }

        .booking-card {
            display: grid;
            gap: 0.85rem;
        }

        .booking-card.active {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14);
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            border-radius: 999px;
            display: inline-flex;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 0.35rem 0.65rem;
            text-transform: uppercase;
        }

        .badge.status {
            background: #ccfbf1;
            color: var(--accent-dark);
        }

        .badge.priority {
            background: #ffedd5;
            color: #9a3412;
        }

        dl {
            display: grid;
            gap: 0.55rem;
            margin: 0;
        }

        .detail-row {
            border-top: 1px solid var(--line);
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            padding-top: 0.55rem;
        }

        dt {
            color: var(--muted);
            font-weight: 700;
        }

        dd {
            font-weight: 800;
            margin: 0;
            text-align: right;
        }

        .reflection {
            background: #f8fafc;
            border-left: 4px solid var(--accent);
            color: var(--muted);
            margin: 0;
            padding: 1rem;
        }

        .site-promo {
            background: linear-gradient(135deg, #121110, #134e4a);
            border: 1px solid rgba(19, 78, 74, 0.4);
            color: #ffffff;
        }

        .site-promo p {
            color: #d1fae5;
            margin: 0;
        }

        .site-promo a {
            color: #fed7aa;
            font-weight: 800;
        }

        code {
            background: #ccfbf1;
            border-radius: 6px;
            color: var(--accent-dark);
            font-weight: 800;
            padding: 0.1rem 0.35rem;
        }
    </style>
</head>
<body>
    <main>
        <section class="hero">
            <a class="back-link" href="/roadmap/module-5">Back to Module 5</a>
            <p class="kicker">Module 5 Assignment 5A - SERGIOARTG</p>
            <h1>Designing Your Own Object Oriented World</h1>
            <p class="intro">An interactive PHP OOP workspace based on my real photography platform: portrait and fashion photography, booking requests, pricing, deposits, availability decisions, and order workflow.</p>
            <div class="hero-actions" aria-label="SERGIOARTG live site links">
                <a class="promo-link primary" href="https://sergioartg.com" target="_blank" rel="noopener noreferrer">View live portfolio</a>
                <a class="promo-link secondary" href="https://sergioartg.com/contact" target="_blank" rel="noopener noreferrer">Book a session</a>
            </div>
        </section>

        <section class="content">
            <div class="metrics" aria-label="Booking pipeline summary">
                <article class="metric">
                    <span>Active objects</span>
                    <strong><?= h((string) count($bookings)) ?></strong>
                </article>
                <article class="metric">
                    <span>Pipeline quote value</span>
                    <strong><?= h(moneyFromCents($totalPipelineCents)) ?></strong>
                </article>
                <article class="metric">
                    <span>Deposit still due</span>
                    <strong><?= h(moneyFromCents($depositDueCents)) ?></strong>
                </article>
            </div>

            <p class="reflection">
                Task Summary: This page creates two <code>PhotographyBooking</code> objects and lets the UI call class methods directly through POST actions. The selected object changes visually after methods such as <code>addRetouchPhotos()</code>, <code>changeStatus()</code>, and <code>recordDepositPayment()</code>.
            </p>

            <article class="panel site-promo">
                <h2>Live Photography Platform</h2>
                <p>
                    This classroom object is modeled after my real photography brand. See the live portfolio and booking entry point at
                    <a href="https://sergioartg.com" target="_blank" rel="noopener noreferrer">sergioartg.com</a>.
                </p>
            </article>

            <section class="workspace" aria-label="Interactive booking method controls">
                <form class="panel" method="post">
                    <?php if (function_exists('csrf_token')) { ?>
                        <input type="hidden" name="_token" value="<?= h((string) csrf_token()) ?>">
                    <?php } ?>
                    <h2>Run Class Methods</h2>
                    <label>
                        Select booking object
                        <select name="booking">
                            <option value="portrait"<?= selected($selectedBookingKey, 'portrait') ?>>Portrait client - Maya Chen</option>
                            <option value="fashion"<?= selected($selectedBookingKey, 'fashion') ?>>Fashion client - Northline Studio</option>
                        </select>
                    </label>

                    <div class="method-grid">
                        <button class="secondary" type="submit" name="action" value="summary">Run getSummary()</button>
                        <button type="submit" name="action" value="quote">Run calculateTotalCents()</button>
                        <button class="warm" type="submit" name="action" value="decision">Run getBookingDecision()</button>
                        <button class="secondary" type="submit" name="action" value="priority">Run getPriorityLabel()</button>
                    </div>

                    <label>
                        Extra edited photos for addRetouchPhotos()
                        <input name="extra_photos" type="number" min="1" max="40" value="<?= h((string) $extraPhotoCount) ?>">
                    </label>
                    <button type="submit" name="action" value="add_retouch">Run addRetouchPhotos()</button>

                    <label>
                        Deposit amount for recordDepositPayment()
                        <input name="deposit_amount" type="number" min="1" max="5000" step="25" value="<?= h(number_format($depositAmountCents / 100, 0, '.', '')) ?>">
                    </label>
                    <button type="submit" name="action" value="deposit">Run recordDepositPayment()</button>

                    <label>
                        New status for changeStatus()
                        <select name="new_status">
                            <option value="new"<?= selected($newStatus, 'new') ?>>new</option>
                            <option value="quoted"<?= selected($newStatus, 'quoted') ?>>quoted</option>
                            <option value="deposit_paid"<?= selected($newStatus, 'deposit_paid') ?>>deposit_paid</option>
                            <option value="confirmed"<?= selected($newStatus, 'confirmed') ?>>confirmed</option>
                            <option value="completed"<?= selected($newStatus, 'completed') ?>>completed</option>
                        </select>
                    </label>
                    <button type="submit" name="action" value="status">Run changeStatus()</button>
                </form>

                <section class="panel">
                    <h2>Method Result</h2>
                    <div class="result">
                        <p class="trace"><?= h($methodTrace) ?></p>
                        <p class="output"><?= h($methodOutput) ?></p>
                    </div>

                    <article class="booking-card active">
                        <div class="badge-row">
                            <span class="badge status"><?= h($selectedBooking->status) ?></span>
                            <span class="badge priority"><?= h($selectedBooking->getPriorityLabel()) ?></span>
                        </div>
                        <h3><?= h($selectedBooking->clientName) ?> - <?= h($selectedBooking->serviceType) ?></h3>
                        <dl>
                            <div class="detail-row">
                                <dt>Photographer</dt>
                                <dd><?= h($selectedBooking->photographerName) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Brand</dt>
                                <dd><?= h($selectedBooking->brandName) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Package</dt>
                                <dd><?= h($selectedBooking->packageName) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Quote total</dt>
                                <dd><?= h(moneyFromCents($selectedBooking->calculateTotalCents())) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Deposit due</dt>
                                <dd><?= h(moneyFromCents($selectedBooking->calculateDepositDueCents())) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Edited photos</dt>
                                <dd><?= h((string) $selectedBooking->retouchedPhotos) ?></dd>
                            </div>
                        </dl>
                    </article>
                </section>
            </section>

            <section class="booking-grid" aria-label="Instantiated booking objects">
                <?php foreach ($bookings as $bookingKey => $booking) { ?>
                    <article class="booking-card<?= $bookingKey === $selectedBookingKey ? ' active' : '' ?>">
                        <div class="badge-row">
                            <span class="badge status"><?= h($booking->status) ?></span>
                            <span class="badge priority"><?= h($booking->getPriorityLabel()) ?></span>
                        </div>
                        <h2><?= h($booking->clientName) ?></h2>
                        <p class="output"><?= h($booking->getSummary()) ?></p>
                        <dl>
                            <div class="detail-row">
                                <dt>Client email</dt>
                                <dd><?= h($booking->clientEmail) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Total</dt>
                                <dd><?= h(moneyFromCents($booking->calculateTotalCents())) ?></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Decision</dt>
                                <dd><?= h($booking->getBookingDecision()) ?></dd>
                            </div>
                        </dl>
                    </article>
                <?php } ?>
            </section>

            <article class="panel">
                <h2>AI Method Critique</h2>
                <p class="output">
                    The method generated with AI was <code>getPriorityLabel()</code>. The exact prompt, raw generated code, critique, and changes made are saved in <code>assignments/module5a/critique.md</code>.
                </p>
            </article>
        </section>
    </main>
</body>
</html>
