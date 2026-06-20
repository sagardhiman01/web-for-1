<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=laravel', 'root', '');
    echo "CONNECTED SUCCESSFUL\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
