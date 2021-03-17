<?php

namespace App\Plugin\Alipay;

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
        // 新增支付方式
        if (!DB::table('payway')->where('key', 'alipay')->first()) {
            DB::table('payway')->insert([
                'key' => 'alipay',
                'name' => '支付宝支付',
                'plugin_key' => 'Alipay',
                'icon' => 'icon-alipay',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
