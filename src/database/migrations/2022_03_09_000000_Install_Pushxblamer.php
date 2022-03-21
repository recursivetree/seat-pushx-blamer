<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use RecursiveTree\Seat\PushxBlamer\Jobs\UpdatePushxQueue;
use Seat\Services\Models\Schedule;

class InstallPushxblamer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schedule = new Schedule();
        $schedule->command = "pushxblamer:update";
        $schedule->expression = "0/30 * * * *";
        $schedule->allow_overlap = 0;
        $schedule->allow_maintenance = 0;

        $schedule->save();

        UpdatePushxQueue::dispatch();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


    }
}
