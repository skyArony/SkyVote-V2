<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DB\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use App\Models\DB\ActivityRecord;

class ActivityController extends Controller
{
    // 指定中间件
    public function __construct()
    {
//        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Activity::all();
        return $this->setResponse($data, 200, 0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 校验
        $validatedData = $request->validate([
            'name' => 'required|max:50',
            'intro' => 'required|max:512',
            'refresh_period' => 'required|integer|min:1',
            'refresh_chance' => 'required|integer|min:1',
            'user_from' => 'required|in:QQ,Weibo,Wechat,Other',
            'type' => 'required|in:img,video,audio,link',
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after_or_equal:start_at',
            'host' => 'max:100',
            'undertake' => 'max:100',
            'sponsored' => 'max:100'
        ]);

        $user = auth('api')->user();

        $activity = new Activity;
        // $activity->creator = $user->email;

        $activity->name = $request['name'];
        $activity->intro = $request['intro'];
        $activity->user_from = $request['user_from'];
        $activity->type = $request['type'];
        $activity->start_at = $request['start_at'];
        $activity->end_at = $request['end_at'];
        $activity->refresh_period = $request['refresh_period'];
        $activity->refresh_chance = $request['refresh_chance'];
        isset($request['host']) ? $activity->host = $request['host'] : 1;
        isset($request['undertake']) ? $activity->undertake = $request['undertake'] : 1;
        isset($request['sponsored']) ? $activity->sponsored = $request['sponsored'] : 1;
        isset($request['rules']) ? $activity->rules = $request['rules'] : 1;
        isset($request['backimg']) ? $activity->backimg = $request['backimg'] : 1;
        isset($request['logo']) ? $activity->logo = $request['logo'] : 1;

        if($activity->save()) {
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
            Redis::sadd("voters:".$activity->id, 'init');
            Redis::EXPIREAT('voters:'.$activity->id, strtotime($activity->end_at));
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
            return $this->setResponse($activity, 200, 0);
        } else {
            return $this->setResponse(null, 500, -5001);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $keys = explode(',', $id);
        $data = Activity::find($keys);
        return $this->setResponse($data, 200, 0);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // 校验
        $validatedData = $request->validate([
            'name' => 'max:50',
            'intro' => 'max:512',
            'refresh_period' => 'integer|min:1',
            'refresh_chance' => 'integer|min:1',
            'user_from' => 'in:QQ,Weibo,Wechat,Other',
            'type' => 'in:img,video,audio,link',
            'start_at' => 'date',
            'end_at' => 'date|after_or_equal:start_at',
            'host' => 'max:100',
            'undertake' => 'max:100',
            'sponsored' => 'max:100'
        ]);

        $activity = Activity::find($request->activity);

        isset($request['name']) ? $activity->name = $request['name'] : 1;
        isset($request['intro']) ? $activity->intro = $request['intro'] : 1;
        isset($request['host']) ? $activity->host = $request['host'] : 1;
        isset($request['undertake']) ? $activity->undertake = $request['undertake'] : 1;
        isset($request['sponsored']) ? $activity->sponsored = $request['sponsored'] : 1;
        isset($request['refresh_period']) ? $activity->refresh_period = $request['refresh_period'] : 1;
        isset($request['refresh_chance']) ? $activity->refresh_chance = $request['refresh_chance'] : 1;
        isset($request['user_from']) ? $activity->user_from = $request['user_from'] : 1;
        isset($request['rules']) ? $activity->rules = $request['rules'] : 1;
        isset($request['type']) ? $activity->type = $request['type'] : 1;
        isset($request['backimg']) ? $activity->backimg = $request['backimg'] : 1;
        isset($request['logo']) ? $activity->logo = $request['logo'] : 1;
        isset($request['start_at']) ? $activity->start_at = $request['start_at'] : 1;
        isset($request['end_at']) ? $activity->end_at = $request['end_at'] : 1;

        if($activity->save()) {
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
            // 活动选民key集合--过期时间为活动结束
            Redis::EXPIREAT('voters:'.$activity->id, strtotime($activity->end_at));
            // 活动票数计数器，按票数排序获取时：升序剪掉第一个，逆序剪掉倒数第一个
            Redis::EXPIREAT('ballots:'.$activity->id, strtotime($activity->end_at));
            return $this->setResponse($activity, 200, 0);
        } else {
            return $this->setResponse(null, 500, -5002);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keys = explode(',', $id);
        Activity::destroy($keys);
        return $this->setResponse(null, 204, 0);
    }

    // public function 
}
