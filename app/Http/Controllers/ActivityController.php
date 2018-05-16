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
        $this->middleware('jwt.auth');
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
            'host' => 'max:100',
            'undertake' => 'max:100',
            'sponsored' => 'max:100',
            'refresh_period' => 'integer|min:1',
            'refresh_chance' => 'integer|min:1',
            'user_from' => 'required|integer|in:1,2,3,4',
            'rules' => 'json',
            'ava_type' => 'required|json',
            'backimg' => 'url|max:512',
            'logo' => 'url|max:512',
            'start_at' => 'date|after:now',
            'end_at' => 'date|after_or_equal:start_at'
        ]);

        $user = auth('api')->user();
        $uuid = Str::uuid();

        $activity = new Activity;
        $activity->uniquekey = $uuid;
        $activity->creator = $user->email;

        $activity->name = $validatedData['name'];
        $activity->intro = $validatedData['intro'];
        $activity->host = $validatedData['host'];
        $activity->undertake = $validatedData['undertake'];
        $activity->sponsored = $validatedData['sponsored'];
        isset($validatedData['refresh_period']) ? $activity->refresh_period = $validatedData['refresh_period'] : 1;    // 在值不为空时才赋值，这里的 1 无意义
        isset($validatedData['refresh_chance']) ? $activity->refresh_chance = $validatedData['refresh_chance'] : 1;
        $activity->user_from = $validatedData['user_from'];
        $activity->rules = $validatedData['rules'];
        $activity->ava_type = $validatedData['ava_type'];
        isset($validatedData['backimg']) ? $activity->backimg = $validatedData['backimg'] : 1;
        isset($validatedData['logo']) ? $activity->logo = $validatedData['logo'] : 1;
        isset($validatedData['start_at']) ? $activity->start_at = $validatedData['start_at'] : 1;
        isset($validatedData['end_at']) ? $activity->end_at = $validatedData['end_at'] : 1;

        if($activity->save()) {
            $data = Activity::find($uuid);
            $activity_info = $data->refresh_period . "." . $data->refresh_chance . "." . $data->start_at . "." . $data->end_at;
            // 设置活动信息--这个关系到投票时间的判断和投票机会的判断
            Redis::set('activity_info:'.$data->uniquekey, $activity_info);
            Redis::EXPIREAT('activity_info:'.$data->uniquekey, strtotime($data->end_at));
            // 设置活动参与者信息--初始过期时间为第一个周期结束
            Redis::hset('vote_record:'.$data->uniquekey, 'init', 'init');
            $start = strtotime($data->start_at);
            $exp = $start + $data->refresh_period * 86400;
            Redis::EXPIREAT('vote_record:'.$data->uniquekey, $exp);
            // 活动候选人key集合--过期时间为活动结束
            Redis::sadd("candidates:".$data->uniquekey, 'init');
            Redis::EXPIREAT('candidates:'.$data->uniquekey, strtotime($data->end_at));
            // 活动选民key集合--过期时间为活动结束
            Redis::sadd("voters:".$data->uniquekey, 'init');
            Redis::EXPIREAT('voters:'.$data->uniquekey, strtotime($data->end_at));
            // 活动票数计数器，按票数排序获取时：升序剪掉第一个，逆序剪掉倒数第一个
            Redis::zadd("ballots:".$data->uniquekey, -1, 'init');
            Redis::EXPIREAT('ballots:'.$data->uniquekey, strtotime($data->end_at));
            // uv 和 pv
            Redis::zadd("activity_uv", 0, $data->uniquekey);
            Redis::zadd("activity_pv", 0, $data->uniquekey);
            $activityRecord = new ActivityRecord;
            $activityRecord->activity_key = $data->uniquekey;
            $activityRecord->pv = 0;
            $activityRecord->uv = 0;
            $activityRecord->save();
            return $this->setResponse($data, 200, 0);
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
            'host' => 'max:100',
            'undertake' => 'max:100',
            'sponsored' => 'max:100',
            'refresh_period' => 'integer|min:1',
            'refresh_chance' => 'integer|min:1',
            'user_from' => 'integer|in:1,2,3,4',
            'rules' => 'json',
            'ava_type' => 'json',
            'backimg' => 'url',
            'logo' => 'url',
            'start_at' => 'date',
            'end_at' => 'date|after_or_equal:start_at'
        ]);

        $activity = Activity::find($request->activity);

        isset($validatedData['name']) ? $activity->name = $validatedData['name'] : 1;
        isset($validatedData['intro']) ? $activity->intro = $validatedData['intro'] : 1;
        isset($validatedData['host']) ? $activity->host = $validatedData['host'] : 1;
        isset($validatedData['undertake']) ? $activity->undertake = $validatedData['undertake'] : 1;
        isset($validatedData['sponsored']) ? $activity->sponsored = $validatedData['sponsored'] : 1;
        isset($validatedData['refresh_period']) ? $activity->refresh_period = $validatedData['refresh_period'] : 1;
        isset($validatedData['refresh_chance']) ? $activity->refresh_chance = $validatedData['refresh_chance'] : 1;
        isset($validatedData['user_from']) ? $activity->user_from = $validatedData['user_from'] : 1;
        isset($validatedData['rules']) ? $activity->rules = $validatedData['rules'] : 1;
        isset($validatedData['ava_type']) ? $activity->ava_type = $validatedData['ava_type'] : 1;
        isset($validatedData['backimg']) ? $activity->backimg = $validatedData['backimg'] : 1;
        isset($validatedData['logo']) ? $activity->logo = $validatedData['logo'] : 1;
        isset($validatedData['start_at']) ? $activity->start_at = $validatedData['start_at'] : 1;
        isset($validatedData['end_at']) ? $activity->end_at = $validatedData['end_at'] : 1;

        if($activity->save()) {
            $data = Activity::find($request->activity);
            $activity_info = $data->refresh_period . "." . $data->refresh_chance . "." . $data->start_at . "." . $data->end_at;
            // 设置活动信息--过期时间为活动结束时间--这个关系到投票时间的判断和投票机会的判断
            Redis::set('activity_info:'.$data->uniquekey, $activity_info);
            Redis::EXPIREAT('activity_info:'.$data->uniquekey, strtotime($data->end_at));
            // 设置活动参与者信息--初始过期时间为活动结束
            $start = strtotime($data->start_at);
            $exp = $start + $data->refresh_period * 86400;
            Redis::EXPIREAT('vote_record:'.$data->uniquekey, strtotime($data->end_at));
            // 活动候选人key集合--过期时间为活动结束
            Redis::EXPIREAT('candidates:'.$data->uniquekey, strtotime($data->end_at));
            // 活动选民key集合--过期时间为活动结束
            Redis::EXPIREAT('voters:'.$data->uniquekey, strtotime($data->end_at));
            // 活动票数计数器，按票数排序获取时：升序剪掉第一个，逆序剪掉倒数第一个
            Redis::EXPIREAT('ballots:'.$data->uniquekey, strtotime($data->end_at));
            return $this->setResponse($data, 200, 0);
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
