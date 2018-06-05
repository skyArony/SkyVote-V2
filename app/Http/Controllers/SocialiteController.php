<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Overtrue\Socialite\SocialiteManager;
use App\Models\DB\Voter;
use Illuminate\Support\Facades\Redis;

class SocialiteController extends Controller
{
    public $config;
    public $socialite;

    public function __construct()
    {
        $this->config = [
            'qq' => [
                'client_id'     => env('client_id_qq'),
                'client_secret' => env('client_secret_qq'),
                'redirect'      => 'http://vote.yfree.ccc/socialite/callback?driver=qq',
            ],
        ];

        $this->socialite = new SocialiteManager($this->config);
    }

    public function QQ(Request $request) {
        if(!Redis::EXISTS('activity_info:'.$request->activity_id)) return $this->setResponse(null, 400, -4005);
        $response = $this->socialite->driver('qq')->redirect();
        echo $response;
    }

    public function callback(Request $request){
        $user = $this->socialite->driver($request->driver)->user();

        if(!Voter::find($user['id'])) {
            $voter = new Voter;
            switch ($request->driver) {
                case 'qq':
                    $voter->uniquekey = $user['id'];
                    $voter->name = $user['name'];
                    $voter->avatar = $user['avatar'];
                    $voter->plat_from = 'QQ';
                    $voter->detail = json_encode($user, JSON_UNESCAPED_UNICODE);
                    if(!$voter->save()) return $this->setResponse(null, 500, -4011);
                    else Redis::sadd("voters", $user['id']);
                    break;
            }
        }

        return $user;
    }

}
