<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gateways') || !Schema::hasTable('gateway_currencies')) {
            return;
        }

        $coinPaymentsGateway = DB::table('gateways')->where('code', 503)->first();

        if (!$coinPaymentsGateway) {
            return;
        }

        $now = now();

        DB::table('gateways')->where('code', 503)->update([
            'status' => 1,
            'updated_at' => $now,
        ]);

        DB::table('gateways')->where('code', 1000)->update([
            'status' => 0,
            'updated_at' => $now,
        ]);

        $parameters = json_decode($coinPaymentsGateway->gateway_parameters ?? '{}', true);
        if (!is_array($parameters)) {
            $parameters = [];
        }

        if (!isset($parameters['ipn_secret']) || !is_array($parameters['ipn_secret'])) {
            $parameters['ipn_secret'] = [
                'title' => 'IPN Secret',
                'global' => true,
                'value' => '',
            ];
        } else {
            $parameters['ipn_secret']['title'] = $parameters['ipn_secret']['title'] ?? 'IPN Secret';
            $parameters['ipn_secret']['global'] = true;
            $parameters['ipn_secret']['value'] = $parameters['ipn_secret']['value'] ?? '';
        }

        DB::table('gateways')->where('code', 503)->update([
            'gateway_parameters' => json_encode($parameters),
            'updated_at' => $now,
        ]);

        $currencyGatewayParams = [];
        foreach ($parameters as $key => $config) {
            if (!is_array($config)) {
                continue;
            }

            if (($config['global'] ?? false) === true) {
                $currencyGatewayParams[$key] = $config['value'] ?? '';
            }
        }

        DB::table('gateway_currencies')->where('method_code', 503)->delete();

        DB::table('gateway_currencies')->insert([
            'name' => 'USDT BEP20',
            'currency' => 'USDT.BEP20',
            'symbol' => 'USDT',
            'method_code' => 503,
            'gateway_alias' => $coinPaymentsGateway->alias ?: 'Coinpayments',
            'min_amount' => 50,
            'max_amount' => 50000,
            'percent_charge' => 0,
            'fixed_charge' => 0,
            'rate' => 1,
            'gateway_parameter' => json_encode($currencyGatewayParams),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // Intentionally left non-destructive: this migration updates existing payment settings.
    }
};
