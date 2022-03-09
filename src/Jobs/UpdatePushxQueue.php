<?php

namespace RecursiveTree\Seat\PushxBlamer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;


class UpdatePushxQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function tags()
    {
        return ["pushxqueue"];
    }

    public function handle(){
        $client = new Client([
            'timeout'  => 5.0,
        ]);
        $res = $client->request('GET', 'https://koe-eve.com/api/pushx/queue');

        if($res->getStatusCode() !== 200){
            $this->fail(new Exception("Failed to load PushX queue status!"));
            return;
        }

        //decode and reencode to make sure it is correct
        $data = json_decode($res->getBody());

        setting(["pushxqueuestatus",json_encode($data)], true);
    }
}