<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$planCols = Illuminate\Support\Facades\Schema::getColumnListing('plans');
$investCols = Illuminate\Support\Facades\Schema::getColumnListing('invests');
echo "PLAN_COLUMNS=".implode(',', $planCols).PHP_EOL;
echo "INVEST_COLUMNS=".implode(',', $investCols).PHP_EOL;

$plans = App\Models\Plan::where('status',1)->limit(5)->get();
foreach($plans as $p){
  echo "PLAN id={$p->id} name={$p->name} min={$p->minimum} max={$p->maximum} fixed={$p->fixed_amount} interest={$p->interest} type={$p->interest_type} time={$p->time} time_name={$p->time_name} repeat={$p->repeat_time} lifetime={$p->lifetime} capital_back={$p->capital_back}".PHP_EOL;
}
