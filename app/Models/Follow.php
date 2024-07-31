<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'followed_user_id'];

    public function followUsers(){
        //フォローしているユーザーの取得
        //F.user_id--U.id
        return $this->belongsTo(User::class);
    }
}
