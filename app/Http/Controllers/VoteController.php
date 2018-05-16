<?php

namespace App\Http\Controllers;

use App\Events\VoteSuccess;
use Illuminate\Http\Request;
use App\Models\DB\VoteRecord;
use App\Models\DB\Activity;
use App\Models\DB\Candidate;
use App\Models\DB\Voter;
use Illuminate\Support\Facades\Redis;

class VoteController extends Controller
{
    // 投票
    public function vote(Request $request) {
        // 校验
        $validatedData = $request->validate([
            'voter_key' => 'required',
            'activity_key' => 'required|between:36,36',
            'candidate_key' => 'required|between:36,36'
        ]);


        $voter_key = $request->voter_key;
        $activity_key = $request->activity_key;
        $candidate_key = $request->candidate_key;

        // 不在投票时段
        if(!$activity_info = Redis::get('activity_info:'.$activity_key)) return $this->setResponse(null, 400, -4010);

        // 解析活动信息
        $activity_info = explode('.', $activity_info);
        $period = $activity_info[0];
        $chance = $activity_info[1];
        $start = strtotime($activity_info[2]);
        $end = $activity_info[3];

        // 计算下个周期的开始时间
        $cycs = floor((time() - $start) /  86400 * $period);
        $remain = (time() - $start) % (86400 * $period);
        $days = floor($remain / 86400);
        $start = $start + $cycs * $period * 86400;
        $exp = $start + $days * 86400 + ( $period - $days )* 86400;

        if(!Redis::EXISTS('vote_record:'.$activity_key)) {
            Redis::hset('vote_record:'.$activity_key, 'init', 'init');
            Redis::EXPIREAT('vote_record:'.$activity_key, $exp);
        }

        if($voted = Redis::hget('vote_record:'.$activity_key, $voter_key)) ;
        else {
            Redis::hset('vote_record:'.$activity_key, $voter_key, 0);
            $voted = 0;
        }

        // 已投数+1，票数+1
        if($voted < $chance) {
            if(Redis::ZINCRBY('ballots:'.$activity_key, 1, $candidate_key) && Redis::HINCRBY('vote_record:'.$activity_key, $voter_key, 1)) {
                event(new VoteSuccess($request->ip(), $voter_key, $activity_key, $candidate_key));
                return $this->setResponse("投票成功", 200, 0);
            }
            else
                return $this->setResponse(null, 500, -4012);
        } else {
            return $this->setResponse(null, 400, -4013);
        }
    }
}
