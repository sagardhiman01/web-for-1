<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // pages table
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('slug', 40)->nullable();
                $table->string('tempname', 40)->nullable();
                $table->text('secs')->nullable();
                $table->smallInteger('is_default')->default(0);
                $table->timestamps();
            });
            // Insert default pages
            DB::table('pages')->insert([
                ['name' => 'HOME', 'slug' => '/', 'tempname' => 'templates.basic.', 'secs' => '["about","plan","why_choose","calculation","how_work","faq","testimonial","team","transaction","top_investor","cta","we_accept","blog","subscribe"]', 'is_default' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // admin_notifications table
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title')->nullable();
                $table->text('content')->nullable();
                $table->string('click_url')->nullable();
                $table->tinyInteger('is_read')->default(0);
                $table->timestamps();
            });
        }

        // admin_password_resets table
        if (!Schema::hasTable('admin_password_resets')) {
            Schema::create('admin_password_resets', function (Blueprint $table) {
                $table->string('email', 40)->nullable()->index();
                $table->string('token', 40)->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        // deposits table
        if (!Schema::hasTable('deposits')) {
            Schema::create('deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('method_id')->nullable();
                $table->string('method_currency', 40)->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('method_amount', 28, 8)->default(0);
                $table->decimal('charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->decimal('final_amo', 28, 8)->default(0);
                $table->decimal('after_charge', 28, 8)->default(0);
                $table->string('trx', 40)->nullable();
                $table->text('detail')->nullable();
                $table->text('reject_reason')->nullable();
                $table->smallInteger('status')->default(0);
                $table->string('from_api', 40)->nullable();
                $table->timestamps();
            });
        }

        // device_tokens table
        if (!Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('token')->nullable();
                $table->boolean('is_app')->default(0);
                $table->timestamps();
            });
        }

        // extensions table
        if (!Schema::hasTable('extensions')) {
            Schema::create('extensions', function (Blueprint $table) {
                $table->id();
                $table->string('act', 40)->nullable();
                $table->string('name', 40)->nullable();
                $table->text('description')->nullable();
                $table->string('script')->nullable();
                $table->text('shortcode')->nullable();
                $table->string('support', 40)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // frontends table
        if (!Schema::hasTable('frontends')) {
            Schema::create('frontends', function (Blueprint $table) {
                $table->id();
                $table->string('data_keys', 40)->nullable();
                $table->longText('data_values')->nullable();
                $table->string('tempname', 40)->nullable();
                $table->string('slug', 40)->nullable();
                $table->timestamps();
            });
        }

        // gateways table
        if (!Schema::hasTable('gateways')) {
            Schema::create('gateways', function (Blueprint $table) {
                $table->id();
                $table->string('form_id', 40)->default(0);
                $table->string('code', 40)->nullable();
                $table->string('name', 40)->nullable();
                $table->string('alias', 40)->nullable();
                $table->text('description')->nullable();
                $table->text('image')->nullable();
                $table->string('min_amount', 40)->default(0);
                $table->string('max_amount', 40)->default(0);
                $table->decimal('percent_charge', 5, 2)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->smallInteger('status')->default(1);
                $table->smallInteger('crypto')->default(0);
                $table->text('currencies')->nullable();
                $table->text('extra')->nullable();
                $table->timestamps();
            });
        }

        // gateway_currencies table
        if (!Schema::hasTable('gateway_currencies')) {
            Schema::create('gateway_currencies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('method_code')->nullable();
                $table->string('name', 100)->nullable();
                $table->string('currency', 40)->nullable();
                $table->string('symbol', 40)->nullable();
                $table->decimal('min_amount', 28, 8)->default(0);
                $table->decimal('max_amount', 28, 8)->default(0);
                $table->decimal('percent_charge', 5, 2)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(1);
                $table->text('image')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->text('gateway_parameter')->nullable();
                $table->timestamps();
            });
        }

        // holidays table
        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('date', 40)->nullable();
                $table->string('repeat_every_year', 40)->nullable();
                $table->timestamps();
            });
        }

        // invests table
        if (!Schema::hasTable('invests')) {
            Schema::create('invests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('plan_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('interest', 28, 8)->default(0);
                $table->string('period', 40)->nullable();
                $table->string('time_name', 40)->nullable();
                $table->integer('hours')->nullable();
                $table->timestamp('next_time')->nullable()->default(null);
                $table->decimal('return_rec_amo', 28, 8)->default(0);
                $table->tinyInteger('status')->default(1);
                $table->string('wallet_type', 40)->nullable();
                $table->timestamps();
            });
        }

        // languages table
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('code', 40)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('is_default')->default(0);
                $table->timestamps();
            });
            DB::table('languages')->insert([
                ['name' => 'English', 'code' => 'en', 'status' => 1, 'is_default' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // notification_logs table
        if (!Schema::hasTable('notification_logs')) {
            Schema::create('notification_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('sent_from', 40)->nullable();
                $table->string('sent_to', 40)->nullable();
                $table->string('subject', 255)->nullable();
                $table->text('message')->nullable();
                $table->string('notification_type', 40)->nullable();
                $table->timestamps();
            });
        }

        // notification_templates table
        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
                $table->id();
                $table->string('act', 40)->nullable();
                $table->string('name', 40)->nullable();
                $table->string('subj', 255)->nullable();
                $table->text('email_body')->nullable();
                $table->text('sms_body')->nullable();
                $table->text('shortcodes')->nullable();
                $table->smallInteger('email_status')->default(1);
                $table->smallInteger('sms_status')->default(1);
                $table->smallInteger('firebase_status')->default(0);
                $table->text('firebase_body')->nullable();
                $table->timestamps();
            });
        }

        // plans table
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->decimal('minimum', 28, 8)->default(0);
                $table->decimal('maximum', 28, 8)->default(0);
                $table->decimal('fixed_amount', 28, 8)->default(0);
                $table->decimal('interest', 28, 8)->default(0);
                $table->string('interest_type', 40)->nullable();
                $table->integer('times')->nullable();
                $table->string('time_setting_id', 40)->nullable();
                $table->string('lifetime', 40)->nullable();
                $table->decimal('capital_back', 28, 8)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // promotion_tools table
        if (!Schema::hasTable('promotion_tools')) {
            Schema::create('promotion_tools', function (Blueprint $table) {
                $table->id();
                $table->string('title', 40)->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        // referrals table
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->string('commission_type', 40)->nullable();
                $table->tinyInteger('level')->nullable();
                $table->decimal('percent', 5, 2)->default(0);
                $table->timestamps();
            });
        }

        // subscribers table
        if (!Schema::hasTable('subscribers')) {
            Schema::create('subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email', 40)->nullable();
                $table->timestamps();
            });
        }

        // support_attachments table
        if (!Schema::hasTable('support_attachments')) {
            Schema::create('support_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('support_message_id')->nullable();
                $table->string('attachment')->nullable();
                $table->timestamps();
            });
        }

        // support_messages table
        if (!Schema::hasTable('support_messages')) {
            Schema::create('support_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('support_ticket_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        // support_tickets table
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name', 40)->nullable();
                $table->string('email', 40)->nullable();
                $table->string('ticket')->nullable();
                $table->string('subject')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('priority')->default(0);
                $table->text('last_reply')->nullable();
                $table->timestamps();
            });
        }

        // time_settings table
        if (!Schema::hasTable('time_settings')) {
            Schema::create('time_settings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('time', 40)->nullable();
                $table->timestamps();
            });
        }

        // transactions table
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('post_balance', 28, 8)->default(0);
                $table->string('charge', 40)->default(0);
                $table->string('trx_type', 40)->nullable();
                $table->string('trx', 40)->nullable();
                $table->string('wallet_type', 40)->nullable();
                $table->string('remark', 40)->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        // user_logins table
        if (!Schema::hasTable('user_logins')) {
            Schema::create('user_logins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_ip', 40)->nullable();
                $table->string('longitude', 40)->nullable();
                $table->string('latitude', 40)->nullable();
                $table->string('location')->nullable();
                $table->string('country_code', 40)->nullable();
                $table->string('country', 40)->nullable();
                $table->string('city', 40)->nullable();
                $table->string('browser', 40)->nullable();
                $table->string('os', 40)->nullable();
                $table->timestamps();
            });
        }

        // withdrawals table
        if (!Schema::hasTable('withdrawals')) {
            Schema::create('withdrawals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('method_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('currency', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->decimal('charge', 28, 8)->default(0);
                $table->decimal('final_amount', 28, 8)->default(0);
                $table->decimal('after_charge', 28, 8)->default(0);
                $table->string('trx', 40)->nullable();
                $table->text('detail')->nullable();
                $table->text('reject_reason')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->string('wallet_type', 40)->nullable();
                $table->timestamps();
            });
        }

        // withdraw_methods table
        if (!Schema::hasTable('withdraw_methods')) {
            Schema::create('withdraw_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->text('image')->nullable();
                $table->decimal('min_limit', 28, 8)->default(0);
                $table->decimal('max_limit', 28, 8)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('percent_charge', 5, 2)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->string('currency', 40)->nullable();
                $table->string('description')->nullable();
                $table->text('user_data')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // general_settings table (if still missing)
        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('site_name', 40)->nullable();
                $table->string('cur_text', 40)->nullable();
                $table->string('cur_sym', 40)->nullable();
                $table->string('email_from', 40)->nullable();
                $table->text('email_template')->nullable();
                $table->text('sms_body')->nullable();
                $table->string('sms_from', 40)->nullable();
                $table->string('base_color', 40)->nullable();
                $table->string('secondary_color', 40)->nullable();
                $table->text('mail_config')->nullable();
                $table->text('sms_config')->nullable();
                $table->text('global_shortcodes')->nullable();
                $table->tinyInteger('kv')->default(0);
                $table->tinyInteger('ev')->default(0);
                $table->tinyInteger('en')->default(1);
                $table->tinyInteger('sv')->default(0);
                $table->tinyInteger('sn')->default(1);
                $table->tinyInteger('force_ssl')->default(0);
                $table->tinyInteger('maintenance_mode')->default(0);
                $table->tinyInteger('secure_password')->default(0);
                $table->tinyInteger('agree')->default(0);
                $table->tinyInteger('registration')->default(1);
                $table->string('active_template', 40)->default('basic');
                $table->text('system_info')->nullable();
                $table->tinyInteger('deposit_commission')->default(1);
                $table->tinyInteger('invest_commission')->default(1);
                $table->tinyInteger('invest_return_commission')->default(1);
                $table->decimal('signup_bonus_amount', 28, 8)->default(0);
                $table->tinyInteger('signup_bonus_control')->default(0);
                $table->tinyInteger('promotional_tool')->default(0);
                $table->text('firebase_config')->nullable();
                $table->text('firebase_template')->nullable();
                $table->tinyInteger('push_notify')->default(0);
                $table->string('off_day')->nullable();
                $table->text('last_cron')->nullable();
                $table->tinyInteger('b_transfer')->default(0);
                $table->string('f_charge', 40)->default('0.00000000');
                $table->decimal('p_charge', 5, 2)->default(0);
                $table->tinyInteger('holiday_withdraw')->default(0);
                $table->tinyInteger('language_switch')->default(1);
                $table->timestamps();
            });
            DB::table('general_settings')->insert([
                'site_name' => 'Core Asset', 'cur_text' => 'USD', 'cur_sym' => '$',
                'email_from' => 'noreply@coreasset.com', 'base_color' => '0083ff',
                'secondary_color' => 'd5373f', 'kv' => 0, 'ev' => 0, 'en' => 1,
                'sv' => 0, 'sn' => 1, 'force_ssl' => 0, 'maintenance_mode' => 0,
                'secure_password' => 0, 'agree' => 0, 'registration' => 1,
                'active_template' => 'basic', 'deposit_commission' => 1,
                'invest_commission' => 1, 'invest_return_commission' => 1,
                'signup_bonus_amount' => 0, 'signup_bonus_control' => 0,
                'promotional_tool' => 0, 'push_notify' => 0, 'b_transfer' => 0,
                'f_charge' => '0.00000000', 'p_charge' => 0, 'holiday_withdraw' => 0,
                'language_switch' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        // These are base tables - don't drop in rollback
    }
};
