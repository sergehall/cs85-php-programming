<?php
/*
 * Module 4 Assignment 4B: Personal Inventory Database
 * Student: Serge Hall
 *
 * This script displays personal inventory records from a MySQL database using
 * PDO, a prepared SELECT statement, and escaped HTML output.
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
 *
 * Reflection:
 * I chose practical items that could belong in a student workspace and home lab:
 * computer equipment, class materials, tools, art supplies, and kitchen gear.
 * A real inventory system could scale by adding users, locations, suppliers,
 * low-stock alerts, barcode scanning, audit history, and role-based access.
 * PDO helps protect against SQL injection because prepared statements separate
 * the SQL command from data values instead of mixing user input directly into
 * the query string.
 *
 * Local project connection:
 * Host: 127.0.0.1
 * Port: 3307
 * Database: inventory_db
 * User: cs85
 */

$databaseHost = '127.0.0.1';
$databasePort = '3307';
$databaseName = 'inventory_db';
$databaseUser = 'cs85';
$databasePassword = 'cs85_password';
$items = [];
$categories = [];
$connectionMessage = 'Inventory has not loaded yet.';
$connectionError = '';
$searchTerm = trim((string) ($_GET['search'] ?? ''));
$selectedCategory = trim((string) ($_GET['category'] ?? ''));
$minQuantityInput = trim((string) ($_GET['min_quantity'] ?? ''));
$sortBy = (string) ($_GET['sort'] ?? 'category_name_asc');
$allowedSorts = [
    'category_name_asc' => [
        'label' => 'Category, then item A-Z',
        'sql' => 'category ASC, item_name ASC',
    ],
    'item_name_asc' => [
        'label' => 'Item name A-Z',
        'sql' => 'item_name ASC',
    ],
    'quantity_desc' => [
        'label' => 'Quantity high to low',
        'sql' => 'quantity DESC, item_name ASC',
    ],
    'quantity_asc' => [
        'label' => 'Quantity low to high',
        'sql' => 'quantity ASC, item_name ASC',
    ],
    'purchase_date_desc' => [
        'label' => 'Newest purchase first',
        'sql' => 'purchase_date DESC, item_name ASC',
    ],
    'purchase_date_asc' => [
        'label' => 'Oldest purchase first',
        'sql' => 'purchase_date ASC, item_name ASC',
    ],
];

if (! isset($allowedSorts[$sortBy])) {
    $sortBy = 'category_name_asc';
}

try {
    $dsn = "mysql:host={$databaseHost};port={$databasePort};dbname={$databaseName};charset=utf8mb4";
    $pdo = new PDO($dsn, $databaseUser, $databasePassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $categoryStatement = $pdo->query('SELECT DISTINCT category FROM items ORDER BY category ASC');
    $categories = array_map(static fn (array $row): string => (string) $row['category'], $categoryStatement->fetchAll());

    $where = [];
    $parameters = [];

    // Filter input values are bound as PDO parameters instead of being mixed into SQL.
    if ($searchTerm !== '') {
        $where[] = '(item_name LIKE :search_item OR category LIKE :search_category)';
        $parameters['search_item'] = '%'.$searchTerm.'%';
        $parameters['search_category'] = '%'.$searchTerm.'%';
    }

    if ($selectedCategory !== '') {
        $where[] = 'category = :category';
        $parameters['category'] = $selectedCategory;
    }

    if ($minQuantityInput !== '' && filter_var($minQuantityInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== false) {
        $where[] = 'quantity >= :min_quantity';
        $parameters['min_quantity'] = (int) $minQuantityInput;
    }

    $whereSql = $where === [] ? '' : ' WHERE '.implode(' AND ', $where);
    $orderSql = $allowedSorts[$sortBy]['sql'];

    // ORDER BY uses an allowlist because column names cannot be safely bound as values.
    $statement = $pdo->prepare(
        "SELECT id, item_name, category, quantity, purchase_date
         FROM items{$whereSql}
         ORDER BY {$orderSql}"
    );
    $statement->execute($parameters);
    $items = $statement->fetchAll();
    $itemCount = count($items);
    $connectionMessage = 'Showing '.$itemCount.' inventory '.($itemCount === 1 ? 'item' : 'items').' from MySQL.';
} catch (PDOException $exception) {
    $connectionMessage = 'Could not load inventory records.';
    $connectionError = $exception->getMessage();
}

if (! function_exists('h')) {
    function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('formatPurchaseDate')) {
    function formatPurchaseDate(string $value): string
    {
        $timestamp = strtotime($value);

        return $timestamp === false ? $value : date('M j, Y', $timestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module 4B Personal Inventory Database</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #132025;
            --muted: #59666d;
            --panel: #ffffff;
            --line: #d7dee2;
            --accent: #0f766e;
            --accent-dark: #134e4a;
            --warm: #b45309;
            --soft: #eefcf8;
            --danger-bg: #fff1f2;
            --danger-text: #9f1239;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at 12% 0%, rgba(180, 83, 9, 0.14), transparent 26rem),
                radial-gradient(circle at 90% 5%, rgba(15, 118, 110, 0.18), transparent 30rem),
                linear-gradient(135deg, #f8fafc 0%, #eefcf8 58%, #fff7ed 100%);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 24px 70px rgba(19, 32, 37, 0.14);
            margin: 0 auto;
            max-width: 64rem;
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, #132025 0%, #134e4a 62%, #9a3412 100%);
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
            font-size: clamp(2rem, 6vw, 3.5rem);
            line-height: 1;
            margin: 0;
        }

        h2 {
            margin: 0;
        }

        .intro {
            color: #d1fae5;
            margin: 0;
            max-width: 45rem;
        }

        .content {
            display: grid;
            gap: 1.25rem;
            padding: 1.5rem;
        }

        .notice {
            background: var(--soft);
            border: 1px solid #99f6e4;
            border-radius: 10px;
            color: var(--accent-dark);
            font-weight: 700;
            margin: 0;
            padding: 0.9rem 1rem;
        }

        .notice.error {
            background: var(--danger-bg);
            border-color: #fecdd3;
            color: var(--danger-text);
        }

        .filters {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            display: grid;
            gap: 1rem;
            padding: 1rem;
        }

        .filter-grid {
            display: grid;
            gap: 0.85rem;
        }

        @media (min-width: 760px) {
            .filter-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        label {
            color: var(--muted);
            display: grid;
            font-size: 0.82rem;
            font-weight: 800;
            gap: 0.4rem;
            text-transform: uppercase;
        }

        input,
        select {
            border: 1px solid #aab4c4;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            padding: 0.72rem;
            text-transform: none;
        }

        input:focus,
        select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.16);
            outline: none;
        }

        .filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .button {
            background: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            padding: 0.72rem 1rem;
        }

        .button-secondary {
            align-items: center;
            background: #ffffff;
            border: 1px solid #aab4c4;
            border-radius: 8px;
            color: var(--ink);
            display: inline-flex;
            font-weight: 800;
            padding: 0.72rem 1rem;
            text-decoration: none;
        }

        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            min-width: 44rem;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid var(--line);
            padding: 0.85rem 1rem;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .setup-note {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            color: #78350f;
            margin: 0;
            padding: 0.9rem 1rem;
        }
    </style>
</head>
<body>
<main>
    <header>
        <a class="back-link" href="/roadmap/module-4">Back to Module 4</a>
        <p class="kicker">Module 4 Assignment 4B</p>
        <h1>Personal Inventory Database</h1>
        <p class="intro">
            A PHP and MySQL inventory page that connects with PDO, retrieves records
            using a prepared statement, and escapes output before displaying the table.
        </p>
    </header>

    <section class="content">
        <p class="notice">
            <strong>Status:</strong>
            <?php echo h($connectionMessage); ?>
        </p>

        <?php if ($connectionError !== '') { ?>
            <p class="notice error">
                <strong>Connection detail:</strong>
                <?php echo h($connectionError); ?>
            </p>
        <?php } ?>

        <form class="filters" action="" method="GET">
            <div class="filter-grid">
                <label for="search">
                    Search item or category
                    <input
                        type="search"
                        id="search"
                        name="search"
                        value="<?php echo h($searchTerm); ?>"
                        placeholder="keyboard, tools, kitchen..."
                    >
                </label>

                <label for="category">
                    Category
                    <select id="category" name="category">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo h($category); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                                <?php echo h($category); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>

                <label for="min_quantity">
                    Minimum quantity
                    <input
                        type="number"
                        id="min_quantity"
                        name="min_quantity"
                        min="0"
                        value="<?php echo h($minQuantityInput); ?>"
                    >
                </label>

                <label for="sort">
                    Display order
                    <select id="sort" name="sort">
                        <?php foreach ($allowedSorts as $sortKey => $sortOption) { ?>
                            <option value="<?php echo h($sortKey); ?>" <?php echo $sortBy === $sortKey ? 'selected' : ''; ?>>
                                <?php echo h($sortOption['label']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
            </div>

            <div class="filter-actions">
                <button class="button" type="submit">Apply Filters</button>
                <a class="button-secondary" href="/assignments/module4b/show_inventory.php">Reset</a>
            </div>
        </form>

        <h2>Inventory Items</h2>
        <?php if ($items === []) { ?>
            <p>No inventory records match the current filters. Adjust the fields above or reset the list.</p>
        <?php } else { ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Category</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Purchase Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <td><?php echo h((string) $item['item_name']); ?></td>
                            <td><?php echo h((string) $item['category']); ?></td>
                            <td><?php echo h((string) $item['quantity']); ?></td>
                            <td><?php echo h(formatPurchaseDate((string) $item['purchase_date'])); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <h2>Database Setup</h2>
        <p class="setup-note">
            The database is named <strong>inventory_db</strong>. The inventory table
            is named <strong>items</strong> and contains at least five personal
            inventory records. The full SQL setup script is included in the PHP
            comments at the top of this file.
        </p>

        <h2>Reflection</h2>
        <p>
            I chose practical items from a student workspace and home lab because
            they make the inventory feel realistic. This could scale into a real
            system by adding users, locations, suppliers, low-stock alerts, and
            audit history. PDO prepared statements help protect against SQL
            injection by separating SQL commands from data values.
        </p>
    </section>
</main>
</body>
</html>
