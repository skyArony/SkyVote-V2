<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Http\Response;

use App\Models\DB\ActivityRecord;
use App\Models\DB\CandidateRecord;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 构造响应
    public static function setResponse($data, $status, $errcode) {
        $body = array(
            'status_code' => $status,
            'code' => $errcode,
            'message' => self::__getErrMsg($errcode),
            'data' => $data
        );
        $response = new Response($body);
        return $response->setStatusCode($status);
    }

    // errcode 对应的 errmsg
    protected static function __getErrMsg($errcode) {
        $msgForCode = array(
            // 通用部分
            0 => 'Success',   // 请求成功

            // 数据库相关
            -5001 => '新建失败',
            -5002 => '更新失败',

            // 数据输入相关
            -4001 => '缺失img_url',
            -4002 => '缺失video_url',
            -4003 => '缺失audio_url',
            -4004 => '缺失 link_url 或 linkcover_url',
            -4005 => 'activity_key参数错误',

            // 业务相关
            -4010 => '不在可投票时段',
            -4011 => '第三方授权登录失败',
            -4012 => '投票失败',
            -4013 => '投票机会已用完',

            // redis

            // -5000 为框架自动抛出的，token 校验的中间件会用到这个
        );
        return $msgForCode[$errcode];
    }

    public function Test() {
        // 更新 pv 和 uv
        $activity_pv = Redis::zrange('activity_pv', 0, -1, 'WITHSCORES');
        $activity_uv = Redis::zrange('activity_uv', 0, -1, 'WITHSCORES');
        foreach ($activity_pv as $key => $value) {
            $activity_record = ActivityRecord::findOrFail($key);
            $activity_record->pv = $value;
            $activity_record->uv = $activity_uv[$key];
            $activity_record->save();
        }
        // 更新 ballot
        $activitys = Redis::KEYS('ballots:*');
        foreach ($activitys as $value) {
            $ballot = Redis::zrange($value, 0, -1, 'WITHSCORES');
            array_shift($ballot);
            foreach ($ballot as $key => $value2) {
                $candidate_record = CandidateRecord::findOrFail($key);
                $candidate_record->ballot = $value2;
                $candidate_record->save();
            }
        }
    }
}
