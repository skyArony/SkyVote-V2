<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Http\Response;

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

            // -5000 为框架自动抛出的，token 校验的中间件会用到这个
        );
        return $msgForCode[$errcode];
    }
}
