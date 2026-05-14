<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------
        // Admins
        // -----------------------------------------------
        if (DB::table('admins')->count() === 0) {
            DB::table('admins')->insert([
                'id'         => 1,
                'name'       => 'Super Admin',
                'email'      => 'admin@coreasset.com',
                'username'   => 'admin',
                'password'   => '$2y$10$9FlJPX2MB1a4KABCy7Od8eMbgwGMMc01Tvtj/vNyHcwHUpeHZ3gKG', // password: 123456
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -----------------------------------------------
        // General Settings (minimal defaults)
        // -----------------------------------------------
        if (DB::table('general_settings')->count() === 0) {
            DB::table('general_settings')->insert([
                'id'                  => 1,
                'site_name'           => 'Core Asset',
                'cur_text'            => 'USD',
                'cur_sym'             => '$',
                'email_from'          => 'noreply@coreasset.com',
                'email_template'      => '<div>{{message}}</div>',
                'sms_body'            => '{{message}}',
                'base_color'          => '#6571ff',
                'secondary_color'     => '#ff6c6c',
                'sms_from'            => 'CoreAsset',
                'invest_return_time'  => 'daily',
                'kv'                  => 0,
                'en'                  => 1,
                'pn'                  => 1,
                'force_ssl'           => 0,
                'maintenance'         => 0,
                'registration'        => 1,
                'agree'               => 0,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // -----------------------------------------------
        // Plans (sample plans)
        // -----------------------------------------------
        if (DB::table('plans')->count() === 0) {
            $plans = [
                [
                    'name'             => 'Starter',
                    'minimum'          => 50,
                    'maximum'          => 500,
                    'interest'         => 2.5,
                    'interest_type'    => 1,
                    'time'             => 30,
                    'time_name'        => 'Day',
                    'status'           => 1,
                    'featured'         => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
                [
                    'name'             => 'Silver',
                    'minimum'          => 500,
                    'maximum'          => 5000,
                    'interest'         => 3.5,
                    'interest_type'    => 1,
                    'time'             => 30,
                    'time_name'        => 'Day',
                    'status'           => 1,
                    'featured'         => 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
                [
                    'name'             => 'Gold',
                    'minimum'          => 5000,
                    'maximum'          => 50000,
                    'interest'         => 5.0,
                    'interest_type'    => 1,
                    'time'             => 30,
                    'time_name'        => 'Day',
                    'status'           => 1,
                    'featured'         => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
            ];
            DB::table('plans')->insert($plans);
        }

        // -----------------------------------------------
        // Notification Templates (minimal)
        // -----------------------------------------------
        if (DB::table('notification_templates')->count() === 0) {
            $templates = [
                ['act' => 'BAL_ADD',       'name' => 'Balance Add',         'subject' => 'Balance Added',          'email_body' => '<div>{{amount}} added to your account.</div>', 'sms_body' => '{{amount}} added.', 'shortcodes' => '{"amount":"Amount","trx":"Transaction ID"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['act' => 'INVEST',        'name' => 'New Investment',       'subject' => 'Investment Confirmed',   'email_body' => '<div>Your investment of {{amount}} confirmed.</div>', 'sms_body' => 'Investment {{amount}} confirmed.', 'shortcodes' => '{"amount":"Amount","plan":"Plan"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['act' => 'INTEREST',      'name' => 'Interest Credited',    'subject' => 'Interest Credited',      'email_body' => '<div>Interest {{amount}} credited to your account.</div>', 'sms_body' => 'Interest {{amount}} credited.', 'shortcodes' => '{"amount":"Amount"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['act' => 'WITHDRAW',      'name' => 'Withdraw Request',     'subject' => 'Withdrawal Requested',   'email_body' => '<div>Your withdrawal of {{amount}} is under review.</div>', 'sms_body' => 'Withdrawal {{amount}} requested.', 'shortcodes' => '{"amount":"Amount","trx":"Transaction ID"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['act' => 'WITHDRAW_DONE', 'name' => 'Withdraw Approved',    'subject' => 'Withdrawal Approved',    'email_body' => '<div>Your withdrawal of {{amount}} has been approved.</div>', 'sms_body' => 'Withdrawal {{amount}} approved.', 'shortcodes' => '{"amount":"Amount","trx":"Transaction ID"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['act' => 'ADMIN_SUPPORT', 'name' => 'Support Reply',        'subject' => 'Support Ticket Reply',   'email_body' => '<div>Your support ticket has a new reply.</div>', 'sms_body' => 'Support ticket reply received.', 'shortcodes' => '{"ticket_id":"Ticket ID"}', 'email_status' => 1, 'sms_status' => 0, 'created_at' => now(), 'updated_at' => now()],
            ];
            DB::table('notification_templates')->insert($templates);
        }

        // -----------------------------------------------
        // Extensions (minimal - for captcha etc)
        // -----------------------------------------------
        if (DB::table('extensions')->count() === 0) {
            DB::table('extensions')->insert([
                [
                    'act'         => 'custom-captcha',
                    'name'        => 'Custom Captcha',
                    'description' => 'Just put any random string',
                    'image'       => 'customcaptcha.png',
                    'script'      => null,
                    'shortcode'   => '{"random_key":{"title":"Random String","value":"SecureString"}}',
                    'support'     => 'na',
                    'status'      => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'act'         => 'google-recaptcha2',
                    'name'        => 'Google Recaptcha 2',
                    'description' => 'Key location is shown below',
                    'image'       => 'recaptcha3.png',
                    'script'      => '<script src="https://www.google.com/recaptcha/api.js"></script><div class="g-recaptcha" data-sitekey="{{site_key}}" data-callback="verifyCaptcha"></div>',
                    'shortcode'   => '{"site_key":{"title":"Site Key","value":""},"secret_key":{"title":"Secret Key","value":""}}',
                    'support'     => 'recaptcha.png',
                    'status'      => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }
    }
}
