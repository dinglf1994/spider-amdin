<?php
/**
 * Created by PhpStorm.
 * User: Dinglf
 * Date: 2017/4/30 0030
 * Time: 22:38
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Classify extends Model
{
    public function saveAllFile($data)
    {
        $classify = new Classify();
        $classify->saveAll($data);
    }
    public function getPageData($where, $page, $offset)
    {
        $classify = new Classify();
        return $classify->where($where)->field('*')->limit($page, $offset)->select();
    }
    public function getContent($where)
    {
        $classify = new Classify();
        return $classify->where($where)->find();
    }
    // 获取用户标注数组
    public function getLabelArr($number)
    {
        $sql = "SELECT * FROM `cs_classify` WHERE `text_name` NOT IN (SELECT `classify_id` FROM `cs_user_classify` WHERE `user_id` = $number) AND `need_classify` != 2 ORDER BY RAND() LIMIT 5";
        $labelArr = Db::query($sql);
        if (empty($labelArr)) {
            return false;
        }else {
            return $labelArr;
        }
    }
    // feedback
    public function feedback($where, $data, $inc)
    {
        $classify = new Classify();
        $userClassify = new UserClassify();

        $classify->where($where)->setInc($inc, 1);
        $needClassify = $classify->where($where)->field($inc)->find();
        if ($needClassify[$inc] >= 2) {
            $dontNeed = ['need_classify' => 2];
            $classify->save($dontNeed, $where);
        }
        $userClassify->saveAllFile($data);
    }
    // feedback array
    public function feedbackArr($where, $data, $inc)
    {
        $classify = new Classify();
        $userClassify = new UserClassify();
        foreach ($where as $k => $v) {
            $classify->where($v)->setInc($inc[$k], 1);
//            var_dump($belongNum);
            $needClassify = $classify->where($v)->field($inc[$k])->find();
            if ($needClassify[$inc[$k]] >= 2) {
                $dontNeed = ['need_classify' => 2];
                $classify->save($dontNeed, $v);
            }
        }
        $userClassify->saveAll($data);
    }

    // 获取首页图标数据
    public function getIconData()
    {
        $info = [];
        $classify = new Classify();
        $user = model('User');
        $info[] = $classify->where('need_classify = 2')->count();
        $info[] = $classify->where('belong_food != 0 or belong_wine != 0 or belong_meat != 0 or belong_milk != 0')->count();
        $info[] = $classify->where('belong_food = 0 and belong_wine = 0 and belong_meat = 0 and belong_milk = 0')->count();
        $info[] = $user->count();
        foreach ($info as $key => $value) {
            $info[$key] = empty($value) ? 0 : $value;
        }
        return $info;
    }
}