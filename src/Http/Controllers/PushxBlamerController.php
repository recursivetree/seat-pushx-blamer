<?php

namespace RecursiveTree\Seat\PushxBlamer\Http\Controllers;


use RecursiveTree\Seat\PushxBlamer\Helpers\ContractHelper;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Seat\Services\Settings\Seat;

use Carbon\Carbon;


class PushxBlamerController extends Controller
{
    public function main(){

        $blamed = ContractHelper::getBlamedCharacter();
        $queue = json_decode(setting("pushxqueuestatus",true));
        $record = json_decode(setting("pushxqueuerecord", true));

        return view("pushxblamer::main",compact("blamed","queue", "record"));
    }
}