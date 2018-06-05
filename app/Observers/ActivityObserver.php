<?php

namespace App\Observers;

use App\Models\DB\Activity;
use App\Models\DB\ActivityRecord;
use Illuminate\Support\Facades\Redis;

class ActivityObserver
{
    /**
     * 监听创建用户事件.
     *
     */
    public function created(Activity $activity)
    {
        $activity_info = $activity->refresh_period . "." . $activity->refresh_chance . "." . $activity->start_at . "." . $activity->end_at;
        // 设置活动信息--这个关系到投票时间的判断和投票机会的判断
        Redis::set('activity_info:'.$activity->id, $activity_info);
        Redis::EXPIREAT('activity_info:'.$activity->id, strtotime($activity->end_at));
        // 设置活动参与者信息--初始过期时间为第一个周期结束
        Redis::hset('vote_record:'.$activity->id, 'init', 'init');
        $start = strtotime($activity->start_at);
        $exp = $start + $activity->refresh_period * 86400;
        Redis::EXPIREAT('vote_record:'.$activity->id, $exp);
        // 活动候选人key集合--过期时间为活动结束
        Redis::sadd("candidates:".$activity->id, 'init');
        Redis::EXPIREAT('candidates:'.$activity->id, strtotime($activity->end_at));
        // 活动选民key集合--过期时间为活动结束
        Redis::sadd("voters", 'init');
        // 活动票数计数器，按票数排序获取时：升序剪掉第一个，逆序剪掉倒数第一个
        Redis::zadd("ballots:".$activity->id, -1, 'init');
        Redis::EXPIREAT('ballots:'.$activity->id, strtotime($activity->end_at));
        // uv 和 pv
        Redis::zadd("activity_uv", 0, $activity->id);
        Redis::zadd("activity_pv", 0, $activity->id);
        $activityRecord = new ActivityRecord;
        $activityRecord->activity_id = $activity->id;
        $activityRecord->pv = 0;
        $activityRecord->uv = 0;
        $activityRecord->save();
    }

    public function updated(Activity $activity) {
        $activity_info = $activity->refresh_period . "." . $activity->refresh_chance . "." . $activity->start_at . "." . $activity->end_at;
        // 设置活动信息--过期时间为活动结束时间--这个关系到投票时间的判断和投票机会的判断
        Redis::set('activity_info:'.$activity->id, $activity_info);
        Redis::EXPIREAT('activity_info:'.$activity->id, strtotime($activity->end_at));
        // 设置活动参与者信息--初始过期时间为活动结束
        $start = strtotime($activity->start_at);
        $exp = $start + $activity->refresh_period * 86400;
        Redis::EXPIREAT('vote_record:'.$activity->id, strtotime($activity->end_at));
        // 活动候选人key集合--过期时间为活动结束
        Redis::EXPIREAT('candidates:'.$activity->id, strtotime($activity->end_at));
        // 活动票数计数器，按票数排序获取时：升序剪掉第一个，逆序剪掉倒数第一个
        Redis::EXPIREAT('ballots:'.$activity->id, strtotime($activity->end_at));
    }

    public function deleted(Activity $activity) {
        Redis::del('vote_record:'.$activity->id);
        Redis::del('candidates:'.$activity->id);
        Redis::del('ballots:'.$activity->id);
        Redis::del('activity_info:'.$activity->id);
    }

}