<?php

declare(strict_types=1);

use Cs85\Module2A\Application\QuoteTShirtOrder;
use Cs85\Module2A\Domain\Pricing\TShirtPriceCalculator;
use Cs85\Module2A\Presentation\OrderInputFactory;
use Cs85\Module2A\Presentation\ReceiptViewModel;

require_once dirname(__DIR__).'/bootstrap/autoload.php';

$defaults = require dirname(__DIR__).'/config/order.php';
$hasSubmittedOrder = isset($_GET['calculate']);
$config = (new OrderInputFactory)->fromQuery($defaults, $_GET);

$quote = (new QuoteTShirtOrder(new TShirtPriceCalculator))->handle($config);
$viewModel = new ReceiptViewModel($quote);
$pageTitle = 'T-Shirt Price Engine Refactored';
$eyebrow = 'Part B - professional refactor';

require dirname(__DIR__).'/templates/receipt.php';

/*
MY DEBUGGING LOG:
Problem: In Part A, the XL customization handling fee had to apply only when both conditions were true: the shirt was customized and the size was XL. With single-condition if statements, it was easy to place the XL handling fee next to the size upcharge and accidentally charge every XL order.
Solution: I tested the four important cases: XL customized, XL not customized, L customized, and M not customized. That showed the handling fee belonged inside the customization rule. In the professional refactor, I moved pricing rules into TShirtPriceCalculator so this dependency is expressed in one domain class instead of being mixed into the HTML page.
*/
