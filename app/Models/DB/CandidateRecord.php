<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Model;

class CandidateRecord extends Model
{
    protected $connection = 'mysql';  // 多数据库操作时最好进行绑定
    protected $table = 'candidates_record'; // 指定表
    protected $primaryKey = 'id'; // 指定主键
    public $timestamps = false;  // 是否自动维护时间戳
}
