<?php
// logout.php

// Start the session
session_start();
// Destroy the session
session_destroy();
// Redirect to login page or homepage
header("Location: /");
exit;
