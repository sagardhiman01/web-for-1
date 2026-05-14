<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

DB::transaction(function() {
    $transactions = Transaction::where('remark', 'balance_transfer')
        ->where('details', 'like', 'System migration%')
        ->get();
        
    foreach($transactions as $t) {
        $u = User::find($t->user_id);
        if ($u) {
            if (str_contains($t->details, 'Deposit wallet')) {
                $u->deposit_wallet += $t->amount;
                $u->interest_wallet -= $t->amount;
            } else {
                $u->referral_bonus += $t->amount;
                $u->interest_wallet -= $t->amount;
            }
            $u->save();
            $t->delete();
        }
    }
});
echo "Revert complete\n";
