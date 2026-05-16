<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('frontends')) {
            $template = 'bit_gold';
            $data = [
                [
                    'template_name' => $template,
                    'data_keys' => 'banner.content',
                    'data_values' => json_encode([
                        'heading' => 'Invest in the Future of Core Assets',
                        'sub_heading' => 'Secure and Profitable Investment Platform',
                        'button_name' => 'Get Started',
                        'button_link' => 'user/register',
                        'background_image' => 'banner.jpg'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'about.content',
                    'data_values' => json_encode([
                        'heading_w' => 'About',
                        'heading_c' => 'Core Asset',
                        'content' => 'We are a leading investment firm specializing in digital and physical core assets. Our mission is to provide secure earning opportunities for everyone.',
                        'button_name' => 'Read More',
                        'button_link' => 'about',
                        'image' => 'about.jpg'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'calculation.content',
                    'data_values' => json_encode([
                        'heading_w' => 'Profit',
                        'heading_c' => 'Calculator',
                        'sub_heading' => 'Calculate your potential earnings with our advanced profit calculator.'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'plan.content',
                    'data_values' => json_encode([
                        'heading_w' => 'Investment',
                        'heading_c' => 'Plans',
                        'sub_heading' => 'Choose from our wide range of investment plans tailored to your needs.'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'faq.content',
                    'data_values' => json_encode([
                        'heading_w' => 'Frequently Asked',
                        'heading_c' => 'Questions',
                        'sub_heading' => 'Find answers to common questions about our platform.'
                    ])
                ]
            ];

            foreach ($data as $item) {
                if (DB::table('frontends')->where('template_name', $item['template_name'])->where('data_keys', $item['data_keys'])->count() == 0) {
                    DB::table('frontends')->insert(array_merge($item, ['created_at' => now(), 'updated_at' => now()]));
                }
            }
            
            // Add some elements
            $elements = [
                [
                    'template_name' => $template,
                    'data_keys' => 'faq.element',
                    'data_values' => json_encode([
                        'question' => 'How can I deposit?',
                        'answer' => 'You can deposit using various gateways like USDT, TRX, and more from your dashboard.'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'faq.element',
                    'data_values' => json_encode([
                        'question' => 'What is the minimum withdrawal?',
                        'answer' => 'The minimum withdrawal amount varies by method, typically starting from $10.'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'why_choose.element',
                    'data_values' => json_encode([
                        'title' => 'Secure Platform',
                        'description' => 'We use industry-standard encryption to protect your data and funds.',
                        'icon' => 'las la-shield-alt'
                    ])
                ],
                [
                    'template_name' => $template,
                    'data_keys' => 'why_choose.element',
                    'data_values' => json_encode([
                        'title' => 'Fast Payouts',
                        'description' => 'Our automated system ensures your withdrawals are processed quickly.',
                        'icon' => 'las la-bolt'
                    ])
                ]
            ];

            foreach ($elements as $element) {
                if (DB::table('frontends')->where('template_name', $element['template_name'])->where('data_keys', $element['data_keys'])->where('data_values', $element['data_values'])->count() == 0) {
                    DB::table('frontends')->insert(array_merge($element, ['created_at' => now(), 'updated_at' => now()]));
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to remove data on rollback
    }
};
