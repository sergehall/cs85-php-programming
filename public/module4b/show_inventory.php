<?php
/*
 * Module 4 Assignment 4B: Personal Inventory Database
 * Student: Serge Hall
 *
 * This first version creates the assignment page that will display inventory
 * records from a MySQL database using PDO.
 *
 * Database setup SQL:
 *
 * CREATE DATABASE IF NOT EXISTS inventory_db
 *   CHARACTER SET utf8mb4
 *   COLLATE utf8mb4_unicode_ci;
 *
 * USE inventory_db;
 *
 * CREATE TABLE IF NOT EXISTS items (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   item_name VARCHAR(100) NOT NULL,
 *   category VARCHAR(50) NOT NULL,
 *   quantity INT NOT NULL DEFAULT 0,
 *   purchase_date DATE NOT NULL
 * );
 *
 * INSERT INTO items (item_name, category, quantity, purchase_date)
 * VALUES
 *   ('Mechanical Keyboard', 'Electronics', 1, '2025-01-12'),
 *   ('Network Lab Notebook', 'Education', 3, '2025-02-05'),
 *   ('Portable SSD', 'Electronics', 2, '2025-03-18'),
 *   ('Bike Repair Kit', 'Tools', 1, '2025-04-02'),
 *   ('Drawing Markers', 'Art Supplies', 12, '2025-04-28'),
 *   ('Coffee Grinder', 'Kitchen', 1, '2025-05-10');
 */

$databaseHost = '127.0.0.1';
$databaseName = 'inventory_db';
$databaseUser = 'root';
$databasePassword = '';
$items = [];
$connectionMessage = 'Inventory has not loaded yet.';
$connectionError = '';

try {
    $dsn = "mysql:host={$databaseHost};dbname={$databaseName};charset=utf8mb4";
    $pdo = new PDO($dsn, $databaseUser, $databasePassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Prepared statements keep SQL structure separate from data values.
    $statement = $pdo->prepare(
        'SELECT id, item_name, category, quantity, purchase_date
         FROM items
         ORDER BY category ASC, item_name ASC'
    );
    $statement->execute();
    $items = $statement->fetchAll();
    $connectionMessage = 'Loaded '.count($items).' inventory items from MySQL.';
} catch (PDOException $exception) {
    $connectionMessage = 'Could not load inventory records.';
    $connectionError = $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module 4B Personal Inventory Database</title>
</head>
<body>
<main>
    <p><a href="/roadmap/module-4">Back to Module 4</a></p>
    <h1>Personal Inventory Database</h1>
    <p>
        This page will connect to MySQL with PDO and display items from the
        <strong>inventory_db</strong> database.
    </p>

    <p>
        <strong>Status:</strong>
        <?php echo htmlspecialchars($connectionMessage, ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <?php if ($connectionError !== '') { ?>
        <p>
            <strong>Connection detail:</strong>
            <?php echo htmlspecialchars($connectionError, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php } ?>

    <h2>Database Setup</h2>
    <p>
        The database is named <strong>inventory_db</strong>. The inventory table
        is named <strong>items</strong> and contains at least five personal
        inventory records.
    </p>
</main>
</body>
</html>
