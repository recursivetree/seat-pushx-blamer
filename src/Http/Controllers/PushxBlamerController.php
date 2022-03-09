<?php

namespace RecursiveTree\Seat\PushxBlamer\Http\Controllers;


use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Seat\Services\Settings\Seat;

use Carbon\Carbon;


class PushxBlamerController extends Controller
{
    public function main(){

        $blamed = DB::table("contract_details")
            ->selectRaw("IFNULL(main_character_id,issuer_id) as character_id")
            ->selectRaw("COUNT(*) as contract_count")
            ->selectRaw("universe_names.name as character_name")
            ->where("assignee_id",98079862)
            ->where("status","outstanding")
            ->leftJoin("refresh_tokens","contract_details.issuer_id", "=", "refresh_tokens.character_id")
            ->leftJoin("users","refresh_tokens.user_id","=","users.id")
            ->leftJoin("universe_names","entity_id","=",DB::raw("IFNULL(main_character_id,issuer_id)"))
            ->groupBy(DB::raw("IFNULL(main_character_id,issuer_id)"),"universe_names.name")
            ->orderBy("contract_count","desc")
            ->inRandomOrder()
            ->first();

        $queue = json_decode(setting("pushxqueuestatus",true));

        //dd($queue);

        return view("pushxblamer::main",compact("blamed","queue"));
    }
}