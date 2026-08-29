<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

logoutAdmin();
header('Location: ' . baseUrl() . '/frontend/admin/authentication/login.php');
exit;
