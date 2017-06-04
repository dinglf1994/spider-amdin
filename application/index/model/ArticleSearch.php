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

class ArticleSearch extends Model
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

    public function importLabel($label, $md5Url)
    {
        $sql = "UPDATE `cs_article_search` SET `label` = {$label} WHERE `article_md5url` = '{$md5Url}'";
        $articleSearch = new ArticleSearch();
        return $articleSearch->execute($sql);
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

    // 分页查询数据
    public function pageSelect($where, $field = '*', $query = [])
    {
        $classify = new Classify();
        $list = $classify->where($where)->field($field)->order('id DESC')->paginate(15, false, array(
            'query' => $query
        ));
        $page = $list->render();
//        var_dump($page);exit;
        return ['list' => $list, 'page' => $page];
    }

    // 执行自定义sql
    public function executeSql($sql)
    {
        $articleSearch = new ArticleSearch();
        return $articleSearch->execute($sql);
    }

    //获取每类数据的来源网站排名前十的
    public function getWebRank($limit = 10)
    {
        $sql = "SELECT article_source,COUNT(articleid) as total FROM cs_article_search GROUP BY article_source ORDER BY total DESC LIMIT {$limit}";
        $articleSearch = new ArticleSearch();
        return $articleSearch->query($sql);
    }

    // 获取五年的间的数据分布
    public function getYearRank($limit = 6)
    {
        // AND label IN (2,3,4)
        $sql = "SELECT label, COUNT(articleid) as total, STR_TO_DATE(`article_pubtime_str`,'%Y') as y FROM cs_article_search WHERE UNIX_TIMESTAMP(STR_TO_DATE(`article_pubtime_str`,'%Y')) > UNIX_TIMESTAMP(STR_TO_DATE(NOW(),'%Y'))-157852900 AND label IN (2,3,4) GROUP BY STR_TO_DATE(`article_pubtime_str`,'%Y'), label ORDER BY y DESC";
        $articleSearch = new ArticleSearch();
        $yearInfo = $articleSearch->query($sql);
        $label = [];
        if (!empty($yearInfo)) {
            foreach ($yearInfo as $k => $value) {
                $key = substr($value['y'], 0, 4);
                $label[$key][] = ['total' => $value['total'], 'label' => $value['label']];
            }
            return $label;
        }else {
            return 0;
        }
    }
}