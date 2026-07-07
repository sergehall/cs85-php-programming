<?php

declare(strict_types=1);

use Cs85\Module6A\Controllers\BookingPlannerController;

$localAutoload = dirname(__DIR__).'/vendor/autoload.php';
$rootAutoload = dirname(__DIR__, 3).'/vendor/autoload.php';

if (is_file($localAutoload)) {
    require_once $localAutoload;
} elseif (is_file($rootAutoload)) {
    require_once $rootAutoload;
} else {
    spl_autoload_register(static function (string $className): void {
        $prefix = 'Cs85\\Module6A\\';

        if (! str_starts_with($className, $prefix)) {
            return;
        }

        $relativeClass = substr($className, strlen($prefix));
        $filePath = dirname(__DIR__).'/src/'.str_replace('\\', '/', $relativeClass).'.php';

        if (is_file($filePath)) {
            require_once $filePath;
        }
    });
}

$requestMethod = function_exists('request')
    ? (string) request()->method()
    : (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
$requestData = $requestMethod === 'POST' ? $_POST : [];

if ($requestMethod === 'POST' && $requestData === [] && function_exists('request')) {
    $requestData = request()->except('_token');
}

$controller = new BookingPlannerController();

echo $controller->handle($requestData);
