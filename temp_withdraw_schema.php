<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Illuminate\Support\Facades\Schema::getColumnListing('withdraw_methods');
echo implode(',', $cols).PHP_EOL;
$rows = App\Models\WithdrawMethod::where('status',1)->limit(5)->get();
foreach($rows as $r){
 echo "ID={$r->id} name={$r->name} min={$r->min_limit} max={$r->max_limit} fixed={$r->fixed_charge} pct={$r->percent_charge} rate={$r->rate} currency={$r->currency}".PHP_EOL;
}
