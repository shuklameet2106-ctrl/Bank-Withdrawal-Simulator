# Bank Withdrawal Simulator

A small PHP and HTML demo for account creation, login, balance checks, and withdrawals. It is for local practice only and is not suitable for real financial accounts.

## Requirements

- PHP 8.0 or newer

## Run locally

From this folder, start the PHP development server with its router:

```powershell
php -S 127.0.0.1:8000 router.php
```

Open <http://127.0.0.1:8000>. Use a web server that honors `.htaccess` when serving with Apache; the included rules block direct access to the account data file.

## Features

- Create an account with a unique 4-20 digit account number, a 4-12 digit PIN, and an initial deposit.
- Log in and view the account holder name and balance.
- Withdraw money with server-side validation and insufficient-balance checks.
- Store account records in `data/accounts.json`; PINs are saved as password hashes.

The account file should remain private. The included PHP development-server router and Apache access rule block direct requests to the `data` directory.