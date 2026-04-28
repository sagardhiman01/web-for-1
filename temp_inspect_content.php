<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$gs = App\Models\GeneralSetting::first(['active_template']);
echo "ACTIVE_TEMPLATE=" . $gs->active_template . PHP_EOL;

$pages = App\Models\Page::where('tempname', 'templates/'.$gs->active_template.'/')->get(['id','name','slug','secs']);
foreach ($pages as $p) {
  echo "PAGE: {$p->id} | {$p->name} | {$p->slug}" . PHP_EOL;
  echo "SECS=" . $p->secs . PHP_EOL;
}

$teamContent = App\Models\Frontend::where('template_name',$gs->active_template)->where('data_keys','team.content')->first();
$teamElements = App\Models\Frontend::where('template_name',$gs->active_template)->where('data_keys','team.element')->get(['id','data_values']);
$policyPages = App\Models\Frontend::where('template_name',$gs->active_template)->where('data_keys','policy_pages.element')->get(['id','data_values']);

echo "TEAM_CONTENT_ID=" . optional($teamContent)->id . PHP_EOL;
echo "TEAM_CONTENT=" . optional($teamContent)->data_values . PHP_EOL;
echo "TEAM_ELEMENTS_COUNT=" . $teamElements->count() . PHP_EOL;
foreach($teamElements as $e){
  echo "TEAM_ELEMENT_ID={$e->id} VALUES={$e->data_values}" . PHP_EOL;
}

echo "POLICY_COUNT=" . $policyPages->count() . PHP_EOL;
foreach($policyPages as $pp){
  echo "POLICY_ID={$pp->id} VALUES={$pp->data_values}" . PHP_EOL;
}
