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

    // 获取首页图标数据
    public function getIconData($where)
    {
        $info = [];
        $userClassify = new UserClassify();
        $info[] = $userClassify->where($where)->count();
        foreach ($info as $key => $value) {
            $info[$key] = empty($value) ? 0 : $value;
        }
        return $info;
    }

    // 分页查询数据
    public function pageSelect($where, $field = '*', $query = [], $number)
    {
        $field = "uc.id as id, uc.user_id as user_id, uc.classify_id as text_name, uc.suggest as suggest, uc.type as type, uc.creat_time as ucreate_time, c.text_url as text_url, c.come_from as come_from, c.title as title, c.content as content, c.push_time as push_time, c.type as c_type, c.need_classify as need_classify";
        $userClassify = new UserClassify();
        $list = $userClassify
            ->alias('uc')
            ->join('cs_classify c', "uc.user_id = $number AND uc.classify_id = c.text_name")
            ->where($where)->field($field)->order('id DESC')->paginate(15, false, array(
            'query' => $query
        ));
        $page = $list->render();
//        var_dump($page);exit;
        return ['list' => $list, 'page' => $page];
    }

}