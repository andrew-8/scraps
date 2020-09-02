<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeScrapsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraps', function (Blueprint $table) {
            $table->dropForeign(['share_user_id']);
            $table->renameColumn('share_user_id', 'share_user_ids');
            $table->string('share_user_ids')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraps', function (Blueprint $table) {
            //
        });
    }
}
