<?php

namespace App\Plugin\Integral;

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
        Schema::table('wallet', function (Blueprint $table) {
            //每次安装或更新都执行该方法, 需要做字段是否存在的判断
            if (!Schema::hasColumn('wallet', 'integral')) {
                $table->decimal('integral')->default(0)->comment('积分');
            }
        });

        DB::table('wallet')->where('userid', 1)->update(['integral' => 100]);
    }
}
