<?php
require_once '../config/helpers.php';
$_SESSION = [];
session_destroy();
header('Location: ../login.php');
exit;
