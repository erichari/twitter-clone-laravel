<?php

/*
共通ユーティリティクラス
*/
namespace App;

use Illuminate\Http\Request;

class Util{
    /**
    * 指定した日時からどれだけ経過したかを取得
    *
    * @param string $time 日時
    * @return int $response
    */
    public function convertToDayTimeAgo(string $time)
    {
        $unix = strtotime($time);
        $now = time();
        $diff_sec = $now - $unix;
    
        if ($diff_sec < 60) {
            $time = $diff_sec;
            $unit = '秒前';
        } elseif ($diff_sec < 3600) {
            $time = $diff_sec / 60;
            $unit = '分前';
        } elseif ($diff_sec < 86400) {
            $time = $diff_sec / 3600;
            $unit = '時間前';
        } elseif ($diff_sec < 2764800) {
            $time = $diff_sec / 86400;
            $unit = '日前';
        } else {
    
            if (date('Y') !== date('Y', $unix)) {
                $time = date('Y年n月j日', $unix);
            } else {
                $time = date('n月j日', $unix);
            }
            return $time;
        }
    
        return (int)$time . $unit;
    }

}
    ?>