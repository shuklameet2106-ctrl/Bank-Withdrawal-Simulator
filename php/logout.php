<?php
require_once __DIR__ . '/common.php';
app_start_session();
app_require_post();

$_SESSION = [];
session_destroy();
app_json_response(200, ['message' => 'Logged out.']);