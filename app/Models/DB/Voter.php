<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $connection = 'mysql';  // 多数据库操作时最好进行绑定
    protected $table = 'voter_info'; // 指定表
    protected $primaryKey = 'uniquekey'; // 指定主键
    protected $keyType = 'char'; // 主键数据类型
    public $timestamps = false;  // 是否自动维护时间戳
}
