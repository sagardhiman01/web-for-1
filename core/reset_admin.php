<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

$admin = Admin::first();
if ($admin) {
    $admin->password = Hash::make('admin');
    $admin->save();
    echo "Username: " . $admin->username . "\nPassword: admin\n";
} else {
    echo "No admin found!";
}
