<?php
require_once __DIR__ . '/common.php';
app_start_session();
app_require_post();

$name = app_post_string('name');
$accountNumber = app_post_string('account_number');
$pin = app_post_string('pin');
$initialDeposit = app_amount_to_cents(app_post_string('initial_deposit'));

if ($name === '' || strlen($name) > 100) {
    app_json_response(422, ['error' => 'Enter a name up to 100 characters.']);
}

if (!preg_match('/\A\d{4,20}\z/', $accountNumber)) {
    app_json_response(422, ['error' => 'Account number must contain 4 to 20 digits.']);
}

if (!preg_match('/\A\d{4,12}\z/', $pin)) {
    app_json_response(422, ['error' => 'PIN must contain 4 to 12 digits.']);
}

if ($initialDeposit === null || $initialDeposit < 1) {
    app_json_response(422, ['error' => 'Enter an initial deposit greater than zero with up to two decimal places.']);
}

$accounts = app_load_accounts();
if (isset($accounts[$accountNumber])) {
    app_json_response(409, ['error' => 'That account number is already in use.']);
}

$accounts[$accountNumber] = [
    'name' => $name,
    'pin_hash' => password_hash($pin, PASSWORD_DEFAULT),
    'balance_cents' => $initialDeposit,
];
app_save_accounts($accounts);

app_json_response(201, ['message' => 'Account created successfully. You can now log in.']);