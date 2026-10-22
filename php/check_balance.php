<?php
require_once __DIR__ . '/common.php';
app_start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    app_json_response(405, ['error' => 'This endpoint only accepts GET requests.']);
}

[, , $account] = app_current_account();
app_json_response(200, [
    'name' => $account['name'],
    'balance_cents' => $account['balance_cents'],
]);