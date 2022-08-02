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
        $res = $client->request('GET', 'https://www.pushx.net/');

        $status = $res->getStatusCode();
        if($status !== 200){
            $this->fail(new Exception("The PushX website returns with code $status (expected 200 OK)"));
        }

        $html = preg_replace('~\R~u', "\n", $res->getBody());
        $matches = [];
        preg_match_all("/Outstanding.*?(?<outstanding>\d+)/m",$html, $matches);
        if(count($matches["outstanding"])<1){
            $this->fail(new Exception("Failed to grep the outstanding contract data. If there isn't already one, open an issue on github https://github.com/recursivetree/seat-pushx-blamer"));
            return;
        }
        $outstanding = intval($matches["outstanding"][0]);

        $html = preg_replace('~\R~u', "\n", $res->getBody());
        $matches = [];
        preg_match_all("/Completed.*?(?<completed>\d+)/m",$html, $matches);
        if(count($matches["completed"])<1){
            $this->fail(new Exception("Failed to grep the completed contract data. If there isn't already one, open an issue on github https://github.com/recursivetree/seat-pushx-blamer"));
            return;
        }
        $completed = intval($matches["completed"][0]);

        return [
            "outstanding"=>$outstanding,
            "dailycompleted"=>$completed
        ];
    }
}