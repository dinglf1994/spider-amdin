<?php
/**
 * Created by PhpStorm.
 * User: Dinglf
 * Date: 2017/4/30 0030
 * Time: 22:38
 */

namespace app\index\model;


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
}