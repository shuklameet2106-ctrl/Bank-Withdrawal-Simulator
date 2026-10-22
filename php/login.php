<?php
require_once __DIR__ . '/common.php';
app_start_session();
app_require_post();

$accountNumber = app_post_string('account_number');
$pin = app_post_string('pin');

if (!preg_match('/\A\d{4,20}\z/', $accountNumber) || !preg_match('/\A\d{4,12}\z/', $pin)) {
    app_json_response(401, ['error' => 'Incorrect account number or PIN.']);
}

$accounts = app_load_accounts();
$account = $accounts[$accountNumber] ?? null;
if (!is_array($account) || !password_verify($pin, $account['pin_hash'] ?? '')) {
    app_json_response(401, ['error' => 'Incorrect account number or PIN.']);
}

session_regenerate_id(true);
$_SESSION['account_number'] = $accountNumber;
app_json_response(200, ['message' => 'Login successful.']);