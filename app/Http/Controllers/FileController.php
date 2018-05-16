<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // 指定中间件
    public function __construct()
    {
        // $this->middleware('jwt.auth');
    }

    // 上传文件返回地址
    public static function uploadImg(Request $request)
    {
        $files = $request->file();
        $dir = $request->dir;
        $urls = array();

        foreach ($files as $file) {
            $path = Storage::disk('public')->put($dir, $file);
            $urls[] = Storage::disk('public')->url($path);
        }

        return $urls;
    }
}
