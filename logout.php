<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Redirect to thankyou.php
header("Location: thankyou.php");
exit();
?>