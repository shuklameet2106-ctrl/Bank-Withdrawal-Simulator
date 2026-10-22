<?php
require_once __DIR__ . '/common.php';
app_start_session();
app_require_post();

[$accounts, $accountNumber, $account] = app_current_account();
$amount = app_amount_to_cents(app_post_string('amount'));

if ($amount === null || $amount < 1) {
    app_json_response(422, ['error' => 'Enter a withdrawal amount greater than zero with up to two decimal places.']);
}

if ($amount > $account['balance_cents']) {
    app_json_response(422, ['error' => 'Insufficient balance for this withdrawal.']);
}

$accounts[$accountNumber]['balance_cents'] -= $amount;
app_save_accounts($accounts);

app_json_response(200, [
    'message' => 'Withdrawal successful.',
    'balance_cents' => $accounts[$accountNumber]['balance_cents'],
]);