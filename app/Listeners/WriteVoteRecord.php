<?php

namespace App\Listeners;

use App\Events\VoteSuccess;
use http\Env\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\DB\VoteRecord;

class WriteVoteRecord implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  VoteSuccess  $event
     * @return void
     */
    public function handle(VoteSuccess $event)
    {
        $voteRecord = new VoteRecord;
        $voteRecord->ip = $event->ip;
        $voteRecord->voter_key = $event->voter_key;
        $voteRecord->activity_key = $event->activity_key;
        $voteRecord->candidate_key = $event->candidate_key;

        // 地区获取
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$voteRecord->ip;
        $ipData = json_decode(file_get_contents($url));
        if($ipData->code == 0) {
            $voteRecord->area = $ipData->data->country . $ipData->data->region . $ipData->data->city;
        } else{
            $voteRecord->area = null;
        }

        $voteRecord->save();
    }
}
