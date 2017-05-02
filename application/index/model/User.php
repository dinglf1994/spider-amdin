<?php
/**
 * Created by PhpStorm.
 * User: Dinglf
 * Date: 2017/4/29 0029
 * Time: 0:03
 */

namespace app\index\model;


use think\Model;

class User extends Model
{
    /**
     * @param $data
     * @return bool
     */
    public function verify($data)
    {
        $user = new User();
        if ($user->where($data)->find()) {
            return true;
        }else {
            return false;
        }
    }
    // 登陆成功 更新状态
    public function updateLoginState($number)
    {
        $user = new User();
        $where['number'] = $number;
        $data = ['last_login_time' => date('Y-m-d H:i:s')];
        if ($user->where($where)->setInc('login_times', 1) && $this->save($data, $where)) {
            $name = $user->where($where)->field('name, rank')->find();
            return $name;
        }else {
            return false;
        }
    }
}