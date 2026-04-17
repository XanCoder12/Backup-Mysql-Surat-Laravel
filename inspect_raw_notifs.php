<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$notifs = DB::table('notifications')->latest()->take(10)->get();
foreach ($notifs as $n) {
    echo "ID: {$n->id}\n";
    echo "DATA: {$n->data}\n";
    echo "-------------------\n";
}
