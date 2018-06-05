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
            'belong_ac_id' => 'required',
            'url' => '',
            'image' => '',
            'tel' => 'integer',
            'QQ' => 'integer'
        ]);

        $candidate = new Candidate;

        $candidate->name = $request['name'];
        $candidate->intro = $request['intro'];
        $candidate->belong_ac_id = $request['belong_ac_id'];
        isset($request['tel']) ? $candidate->tel = $request['tel'] : 1;    // 在值不为空时才赋值，这里的 1 无意义
        isset($request['QQ']) ? $candidate->QQ = $request['QQ'] : 1;
        isset($request['image']) ? $candidate->imageContent = $request['image'] : 1;
        isset($request['url']) ? $candidate->textContent = $request['url'] : 1;

        if($candidate->save()) {
            return $this->setResponse($candidate, 200, 0);
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
            'url' => '',
            'image' => '',
            'tel' => 'integer',
            'QQ' => 'integer'
        ]);

        $candidate = Candidate::find($request->candidate);

        isset($validatedData['name']) ? $candidate->name = $validatedData['name'] : 1;
        isset($validatedData['intro']) ? $candidate->intro = $validatedData['intro'] : 1;
        isset($validatedData['tel']) ? $candidate->tel = $validatedData['tel'] : 1;
        isset($validatedData['QQ']) ? $candidate->QQ = $validatedData['QQ'] : 1;
        isset($request['image']) ? $candidate->imageContent = $request['image'] : 1;
        isset($request['url']) ? $candidate->textContent = $request['url'] : 1;

        if($candidate->save()) {
            return $this->setResponse($candidate, 200, 0);
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
