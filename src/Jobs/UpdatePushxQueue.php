<?php

namespace RecursiveTree\Seat\PushxBlamer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use RecursiveTree\Seat\PushxBlamer\Helpers\ContractHelper;


class UpdatePushxQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function tags()
    {
        return ["pushxqueue"];
    }

    public function handle(){
        //store queue stats
        $queueStats = $this->fetchQueueStats();
        if($queueStats) {
            setting(["pushxqueuestatus", json_encode($queueStats)], true);
        } else {
            //get last data
            $queueStats = json_decode(setting("pushxqueuestatus",true));
        }

        //check for record-breaking queue blockers
        $blamed_character = ContractHelper::getBlamedCharacter();
        if($blamed_character) {

            //get contract count record
            $record_data = json_decode(setting("pushxqueuerecord", true));
            if (!$record_data) {
                $record = 0; // we apparently don't have any data
            } else {
                $record = $record_data->contract_count;
            }

            if($blamed_character->contract_count >= $record){
                $record_data = [
                    "character_name" => $blamed_character->character_name,
                    "character_id" => $blamed_character->character_id,
                    "contract_count" => $blamed_character->contract_count,
                    "queue_status" => $queueStats
                ];
                setting(["pushxqueuerecord",json_encode($record_data)], true);
            }
        }
    }

    private function fetchQueueStats(){
        $client = new Client([
            'timeout'  => 5.0,
        ]);
        $res = $client->request('GET', 'https://koe-eve.com/api/pushx/queue');

        if($res->getStatusCode() !== 200){
            $this->fail(new Exception("Failed to load PushX queue status!"));
        }

        //decode and reencode to make sure it is correct
        $data = json_decode($res->getBody());

        return $data;
    }
}