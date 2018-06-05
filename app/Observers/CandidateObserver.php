<?php

namespace App\Observers;

use App\Models\DB\Candidate;
use App\Models\DB\CandidateRecord;
use Illuminate\Support\Facades\Redis;

class CandidateObserver
{
    /**
     * 监听创建用户事件.
     */
    public function created(Candidate $candidate)
    {
        Redis::sadd("candidates:".$candidate['belong_ac_id'], $candidate->id);
        $candidateRecord = new CandidateRecord;
        $candidateRecord->candidate_id = $candidate->id;
        $candidateRecord->ballot = 0;
        $candidateRecord->save();
    }

}