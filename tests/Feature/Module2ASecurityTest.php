<?php

declare(strict_types=1);

namespace Tests\Feature;

use Cs85\Module2A\Application\QuoteTShirtOrder;
use Cs85\Module2A\Domain\Pricing\TShirtPriceCalculator;
use Cs85\Module2A\Presentation\OrderInputFactory;
use Cs85\Module2A\Presentation\ReceiptViewModel;
use Tests\TestCase;

class Module2ASecurityTest extends TestCase
{
    public function test_order_input_factory_rejects_untrusted_option_values(): void
    {
        $defaults = require base_path('assignments/module2a/order.php');
        $inputFactory = new OrderInputFactory;

        $config = $inputFactory->fromQuery($defaults, [
            'calculate' => '1',
            'size' => 'XXL',
            'color' => '<script>alert("color")</script>',
            'is_customized' => '1',
            'customer_first_name' => ' <script>alert("name")</script> ',
        ]);

        $this->assertSame($defaults['size'], $config['size']);
        $this->assertSame($defaults['color'], $config['color']);
        $this->assertTrue($config['is_customized']);
        $this->assertSame('<script>alert("name")</script>', $config['customer_first_name']);
    }

    public function test_missing_customization_checkbox_is_handled_as_false(): void
    {
        $defaults = require base_path('assignments/module2a/order.php');
        $inputFactory = new OrderInputFactory;

        $config = $inputFactory->fromQuery($defaults, [
            'calculate' => '1',
            'size' => 'M',
            'color' => 'White',
            'customer_first_name' => 'Alex',
        ]);

        $this->assertFalse($config['is_customized']);
    }

    public function test_receipt_template_escapes_user_controlled_output(): void
    {
        $quoteOrder = new QuoteTShirtOrder(new TShirtPriceCalculator);
        $quote = $quoteOrder->handle([
            'size' => 'M',
            'color' => 'White',
            'is_customized' => false,
            'customer_first_name' => '<script>alert("xss")</script>',
        ]);

        $viewModel = new ReceiptViewModel($quote);
        $pageTitle = 'Security Test <title>';
        $eyebrow = 'Safety <check>';
        $hasSubmittedOrder = true;

        ob_start();
        require base_path('assignments/module2a/receipt.php');
        $html = ob_get_clean();

        $this->assertIsString($html);
        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $html);
        $this->assertStringContainsString('Security Test &lt;title&gt;', $html);
        $this->assertStringContainsString('Safety &lt;check&gt;', $html);
    }
}
