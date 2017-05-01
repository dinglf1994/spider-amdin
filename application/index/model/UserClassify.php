<?php
/**
 * Created by PhpStorm.
 * User: Dinglf
 * Date: 2017/5/1 0001
 * Time: 22:33
 */

namespace app\index\model;


use think\Model;

class UserClassify extends Model
{
    public function saveAllFile($data)
    {
        $userClassify = new UserClassify();
        $userClassify->save($data);
    }
    public function hasFeedback($where)
    {
        $userClassify = new UserClassify();
        if ($userClassify->where($where)->field('id')->find()) {
            return true;
        }else {
            return false;
        }
    }
}