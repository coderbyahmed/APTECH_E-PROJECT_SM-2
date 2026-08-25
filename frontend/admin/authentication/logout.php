<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

logoutAdmin();
header('Location: /Aptech_E_Project_02/sound_management/frontend/admin/authentication/login.php');
exit;
