<?php
header('Content-Type: text/plain');

$dir = __DIR__ . '/core/storage/framework/sessions';
echo "Directory: $dir\n";
echo "Exists: " . (is_dir($dir) ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($dir) ? 'YES' : 'NO') . "\n";
echo "Permissions: " . (is_dir($dir) ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A') . "\n";

$testFile = $dir . '/test_write.txt';
$written = @file_put_contents($testFile, 'test');
if ($written !== false) {
    echo "Write test: SUCCESS\n";
    @unlink($testFile);
} else {
    echo "Write test: FAILED - " . json_encode(error_get_last()) . "\n";
}

session_start();
if (!isset($_SESSION['test_val'])) {
    $_SESSION['test_val'] = time();
    echo "Session initiated. Refresh to test persistence. Value: " . $_SESSION['test_val'] . "\n";
} else {
    echo "Session persisted! Value: " . $_SESSION['test_val'] . "\n";
}
