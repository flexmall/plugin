<?php

namespace App\Plugin\WalletPay;

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration as BaseMigration;
use Hyperf\DbConnection\Db as DB;

class Migration extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 余额支付由 wallet 改成 amount
        if (!DB::table('payway')->where('key', 'amount')->first()) {
            DB::table('payway')->insert([
                'key' => 'amount',
                'name' => '余额支付',
                'admin_name' => '余额支付',
                'plugin_key' => 'WalletPay',
                'icon' => 'icon-erjiye-yucunkuan',
                'status' => '1',
                'sort' => 10,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
