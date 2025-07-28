<?php
session_start();
session_destroy();
header(header: "Location: index.php"); // Redirect to index.php
?>