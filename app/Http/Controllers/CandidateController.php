<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DB\Candidate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use App\Models\DB\CandidateRecord;

class CandidateController extends Controller
{
    // 指定中间件
    public function __construct()
    {
        // $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Candidate::all();
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
            'name' => 'required|max:20',
            'intro' => 'required|max:512',
            'belong_ac' => 'required',
            'type' => 'required|integer|in:1,2,3,4',
            'media_array' => 'required|array',
            'media_array.img_url' => 'array',
            'media_array.video_url' => 'url|max:512',
            'media_array.audio_url' => 'url|max:512',
            'media_array.link_url' => 'url|max:512',
            'media_array.linkcover_url' => 'url|max:512',
            'tel' => 'integer',
            'QQ' => 'integer'
        ]);

        $uuid = Str::uuid();

        $candidate = new Candidate;
        $candidate->uniquekey = $uuid;

        $candidate->name = $validatedData['name'];
        $candidate->intro = $validatedData['intro'];
        $candidate->belong_ac = $validatedData['belong_ac'];
        isset($validatedData['tel']) ? $candidate->tel = $validatedData['tel'] : 1;    // 在值不为空时才赋值，这里的 1 无意义
        isset($validatedData['QQ']) ? $candidate->QQ = $validatedData['QQ'] : 1;
        $candidate->type = $validatedData['type'];

        switch ($validatedData['type']) {
            case 1:
                if (isset($validatedData['media_array']['img_url'])) $candidate->img_url = json_encode($validatedData['media_array']['img_url']);
                else return $this->setResponse(null, 400, -4001);
                break;
            case 2:
                if (isset($validatedData['media_array']['video_url'])) $candidate->video_url = $validatedData['media_array']['video_url'];
                else return $this->setResponse(null, 400, -4002);
                break;
            case 3:
                if (isset($validatedData['media_array']['audio_url'])) $candidate->audio_url = $validatedData['media_array']['audio_url'];
                else return $this->setResponse(null, 400, -4003);
                break;
            case 4:
                if (isset($validatedData['media_array']['link_url']) && isset($validatedData['media_array']['linkcover_url'])) {
                    $candidate->link_url = $validatedData['media_array']['link_url'];
                    $candidate->linkcover_url = $validatedData['media_array']['linkcover_url'];
                }
                else return $this->setResponse(null, 400, -4004);
                break;
        }

        if($candidate->save()) {
            $data = Candidate::find($uuid);
            Redis::sadd("candidates:".$validatedData['belong_ac'], $data->uniquekey);
            $candidateRecord = new CandidateRecord;
            $candidateRecord->candidate_key = $data->uniquekey;
            $candidateRecord->ballot = 0;
            $candidateRecord->save();
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
        $data = Candidate::find($keys);
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
            'name' => 'max:20',
            'intro' => 'max:512',
            'type' => 'integer|in:1,2,3,4',
            'media_array' => 'array',
            'media_array.img_url' => 'array',
            'media_array.video_url' => 'url|max:512',
            'media_array.audio_url' => 'url|max:512',
            'media_array.link_url' => 'url|max:512',
            'media_array.linkcover_url' => 'url|max:512',
            'tel' => 'integer',
            'QQ' => 'integer'
        ]);

        $candidate = Candidate::find($request->candidate);

        isset($validatedData['name']) ? $candidate->name = $validatedData['name'] : 1;
        isset($validatedData['intro']) ? $candidate->intro = $validatedData['intro'] : 1;
        isset($validatedData['type']) ? $candidate->type = $validatedData['type'] : 1;
        isset($validatedData['tel']) ? $candidate->tel = $validatedData['tel'] : 1;
        isset($validatedData['QQ']) ? $candidate->QQ = $validatedData['QQ'] : 1;

        if(isset($validatedData['type'])){
            switch ($validatedData['type']) {
                case 1:
                    if (isset($validatedData['media_array']['img_url'])) $candidate->img_url = json_encode($validatedData['media_array']['img_url']);
                    break;
                case 2:

                    if (isset($validatedData['media_array']['video_url'])) $candidate->video_url = $validatedData['media_array']['video_url'];
                    break;
                case 3:
                    if (isset($validatedData['media_array']['audio_url'])) $candidate->audio_url = $validatedData['media_array']['audio_url'];
                    break;
                case 4:
                    if (isset($validatedData['media_array']['link_url']) && isset($validatedData['media_array']['linkcover_url'])) {
                        $candidate->link_url = $validatedData['media_array']['link_url'];
                        $candidate->linkcover_url = $validatedData['media_array']['linkcover_url'];
                    }
                    break;
            }
        }

        if($candidate->save()) {
            $data = Candidate::find($request->candidate);
            return $this->setResponse($data, 200, 0);
        } else {
            return $this->setResponse(null, 500, -5001);
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
        Candidate::destroy($keys);
        return $this->setResponse(null, 204, 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
}
