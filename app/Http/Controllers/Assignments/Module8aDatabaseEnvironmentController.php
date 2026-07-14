<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PDO;
use PDOException;

final class Module8aDatabaseEnvironmentController extends Controller
{
    private const CONNECTION_QUERY = <<<'SQL'
        SELECT
            DATABASE() AS database_name,
            VERSION() AS mysql_version,
            CURRENT_USER() AS connected_user,
            NOW() AS server_time,
            (
                SELECT COUNT(*)
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
            ) AS table_count
        SQL;

    public function show(): View
    {
        return view('assignments.module8a.database-environment', [
            'connectionQuery' => self::CONNECTION_QUERY,
            'connectionResult' => null,
            'defaults' => $this->defaults(),
        ]);
    }

    public function test(Request $request): View
    {
        $validated = $request->validate([
            'host' => ['required', 'in:127.0.0.1,localhost'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'database' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_]+$/'],
            'username' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        $result = [
            'connected' => false,
            'message' => 'The connection could not be established.',
            'details' => null,
        ];

        try {
            $submittedPassword = $validated['password'] ?? '';
            $password = $submittedPassword !== ''
                ? $submittedPassword
                : (string) config('database.connections.module8a.password', '');

            $pdo = new PDO(
                sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $validated['host'],
                    $validated['port'],
                    $validated['database'],
                ),
                $validated['username'],
                $password,
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3,
                ],
            );

            $statement = $pdo->query(self::CONNECTION_QUERY);
            $details = $statement === false ? null : $statement->fetch();

            $result = [
                'connected' => true,
                'message' => 'Connected to the local MySQL database successfully.',
                'details' => is_array($details) ? $details : null,
            ];
        } catch (PDOException $exception) {
            $result['message'] = $this->friendlyConnectionError($exception);
        }

        return view('assignments.module8a.database-environment', [
            'connectionQuery' => self::CONNECTION_QUERY,
            'connectionResult' => $result,
            'defaults' => [
                'host' => $validated['host'],
                'port' => (string) $validated['port'],
                'database' => $validated['database'],
                'username' => $validated['username'],
            ],
        ]);
    }

    /**
     * @return array{host: string, port: string, database: string, username: string}
     */
    private function defaults(): array
    {
        return [
            'host' => (string) config('database.connections.module8a.host', '127.0.0.1'),
            'port' => (string) config('database.connections.module8a.port', '3307'),
            'database' => (string) config('database.connections.module8a.database', 'orm_practice_db'),
            'username' => (string) config('database.connections.module8a.username', 'module8a'),
        ];
    }

    private function friendlyConnectionError(PDOException $exception): string
    {
        $message = strtolower($exception->getMessage());

        return match (true) {
            str_contains($message, 'unknown database') => 'MySQL is running, but orm_practice_db was not found. Create the database and try again.',
            str_contains($message, 'access denied') => 'MySQL rejected the username or password. Check the local account and try again.',
            str_contains($message, 'connection refused'),
            str_contains($message, 'no such file or directory'),
            str_contains($message, 'server has gone away') => 'MySQL is not reachable. Start it in Laravel Herd or XAMPP and try again.',
            default => 'The local MySQL connection failed. Confirm the host, port, database, and credentials.',
        };
    }
}
