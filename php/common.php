<?php
declare(strict_types=1);

function app_start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function app_json_response(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function app_require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        app_json_response(405, ['error' => 'This endpoint only accepts POST requests.']);
    }
}

function app_post_string(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function app_amount_to_cents(string $amount): ?int
{
    if (!preg_match('/\A(\d{1,9})(?:\.(\d{1,2}))?\z/', $amount, $matches)) {
        return null;
    }

    $whole = (int) $matches[1];
    $fraction = isset($matches[2]) ? (int) str_pad($matches[2], 2, '0') : 0;
    return ($whole * 100) + $fraction;
}

function app_load_accounts(): array
{
    $path = __DIR__ . '/../data/accounts.json';
    $contents = file_get_contents($path);
    $accounts = is_string($contents) ? json_decode($contents, true) : null;

    if (!is_array($accounts)) {
        app_json_response(500, ['error' => 'Account data could not be read.']);
    }

    return $accounts;
}

function app_save_accounts(array $accounts): void
{
    $contents = json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $path = __DIR__ . '/../data/accounts.json';

    if (!is_string($contents) || file_put_contents($path, $contents . PHP_EOL, LOCK_EX) === false) {
        app_json_response(500, ['error' => 'Account data could not be saved.']);
    }
}

function app_current_account(): array
{
    $accountNumber = $_SESSION['account_number'] ?? '';
    if (!is_string($accountNumber) || $accountNumber === '') {
        app_json_response(401, ['error' => 'Please log in to continue.']);
    }

    $accounts = app_load_accounts();
    if (!isset($accounts[$accountNumber])) {
        app_json_response(401, ['error' => 'Please log in to continue.']);
    }

    return [$accounts, $accountNumber, $accounts[$accountNumber]];
}