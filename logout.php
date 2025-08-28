<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

header("Location: index.php");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
exit;
?>
