<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DB\Activity;
use Illuminate\Support\Str;

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
            'name' => 'bail|required|max:50',
            'intro' => 'bail|required|max:512',
            'host' => 'max:100',
            'undertake' => 'max:100',
            'sponsored' => 'max:100',
            'refresh_period' => 'integer|min:1',
            'refresh_ballot' => 'integer|min:1',
            'user_from' => 'required|integer|in:1,2,3,4',
            'rules' => 'json',
            'ava_type' => 'required|json',
            'backimg' => 'url',
            'logo' => 'url',
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
        isset($validatedData['refresh_ballot']) ? $activity->refresh_ballot = $validatedData['refresh_ballot'] : 1;
        $activity->user_from = $validatedData['user_from'];
        $activity->rules = $validatedData['rules'];
        $activity->ava_type = $validatedData['ava_type'];
        isset($validatedData['backimg']) ? $activity->backimg = $validatedData['backimg'] : 1;
        isset($validatedData['logo']) ? $activity->logo = $validatedData['logo'] : 1;
        isset($validatedData['start_at']) ? $activity->start_at = $validatedData['start_at'] : 1;
        isset($validatedData['end_at']) ? $activity->end_at = $validatedData['end_at'] : 1;

        if($activity->save()) {
            $data = Activity::find($uuid);
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
            'refresh_ballot' => 'integer|min:1',
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
        isset($validatedData['refresh_ballot']) ? $activity->refresh_ballot = $validatedData['refresh_ballot'] : 1;
        isset($validatedData['user_from']) ? $activity->user_from = $validatedData['user_from'] : 1;
        isset($validatedData['rules']) ? $activity->rules = $validatedData['rules'] : 1;
        isset($validatedData['ava_type']) ? $activity->ava_type = $validatedData['ava_type'] : 1;
        isset($validatedData['backimg']) ? $activity->backimg = $validatedData['backimg'] : 1;
        isset($validatedData['logo']) ? $activity->logo = $validatedData['logo'] : 1;
        isset($validatedData['start_at']) ? $activity->start_at = $validatedData['start_at'] : 1;
        isset($validatedData['end_at']) ? $activity->end_at = $validatedData['end_at'] : 1;

        if($activity->save()) {
            $data = Activity::find($request->activity);
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

    /////////////////////////////////////////////

    /**
     * Show the form for creating a new resource.
     * api 不需要用这个方法
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * api 不需要用这个方法
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
}
