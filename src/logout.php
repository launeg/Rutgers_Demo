<?php
require_once __DIR__ . '/../lib/auth.php';

sign_out();
header('Location: /src/login.php');
