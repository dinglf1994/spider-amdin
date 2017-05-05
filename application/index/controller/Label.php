<?php
namespace app\index\controller;

use app\index\model\Classify;
use app\index\model\User;
use app\index\model\UserClassify;
use think\Controller;
use think\Request;
use think\Session;

class Label extends Controller
{
    private $_objUser;
    private $_objClassify;
    private $_objUserClassify;
    private $loginCheck = ['login', 'register'];
    const FOOD_TYPE = 1;
    const WINE_TYPE = 2;
    const MEAT_TYPE = 3;
    const MILK_TYPE = 4;
    public function __construct(Request $request, User $user, Classify $classify, UserClassify $userClassify)
    {
        parent::__construct($request);
        if (!in_array(ACTION_NAME, $this->loginCheck)) {
            if (!isLogin()) {
                $this->error('抱歉，您尚未登录，请登录再进行操作。正在跳转至登录页面。', '/');
            }
        }
        $this->_objUser = $user;
        $this->_objClassify = $classify;
        $this->_objUserClassify = $userClassify;
    }

    public function index()
    {
        if (Session::get('rank') < 2) {
            $this->error('访问权限不够，返回中。。。');
        }else {
            $pageFood = Request::instance()->get('pd') ? Request::instance()->get('pd') : 1;
            $pageMeat = Request::instance()->get('pt') ? Request::instance()->get('pt') : 1;
            $pageMilk = Request::instance()->get('pk') ? Request::instance()->get('pk') : 1;
            $pageWine = Request::instance()->get('pn') ? Request::instance()->get('pn') : 1;
            $pageFood < 1 ? 1 : $pageFood;
            $beginFood = ($pageFood - 1) * 10;
            $pageMeat < 1 ? 1 : $pageMeat;
            $beginMeat = ($pageMeat - 1) * 10;
            $pageMilk < 1 ? 1 : $pageMilk;
            $beginMilk = ($pageMilk - 1) * 10;
            $pageWine < 1 ? 1 : $pageWine;
            $beginWine = ($pageWine - 1) * 10;
            $offset = 10;

            // 当前页码
            $page = ['food' => $pageFood,
                'meat' => $pageMeat,
                'milk' => $pageMilk,
                'wine' => $pageWine
            ];

            $number = intval(Session::get('userNumber'));

            // 食品
            $where = ['type' => 1];
            $foodFilePage = $this->_objClassify->getPageData($where, $beginFood, $offset);
            foreach ($foodFilePage as $key => $item) {
//            $content = $name['content'];
                $title = $item['title'];
                $name = $item['text_name'];
                $needClassify = $item['need_classify'];
                $hasFeedback = ['classify_id' => $name, 'user_id' => $number];
                $iNeed = 0;
                if ($this->_objUserClassify->hasFeedback($hasFeedback)) {
                    $iNeed = 1;
                }
                $fileData['foodFile']['files'][] = ['id' =>$item['id'], 'name' =>$title, 'file' => $name, 'need_classify' => $needClassify, 'i_need' => $iNeed];
            }
            $fileData['foodFile']['count'] = $this->_objClassify->where($where)->count();

            // 肉类
            $where = ['type' => 3];
            $meatFilePage = $this->_objClassify->getPageData($where, $beginMeat, $offset);
            foreach ($meatFilePage as $key => $item) {
//            $content = $name['content'];
                $title = $item['title'];
                $name = $item['text_name'];
                $needClassify = $item['need_classify'];
                $hasFeedback = ['classify_id' => $name, 'user_id' => $number];
                $iNeed = 0;
                if ($this->_objUserClassify->hasFeedback($hasFeedback)) {
                    $iNeed = 1;
                }
                $fileData['meatFile']['files'][] = ['id' =>$item['id'], 'name' =>$title, 'file' => $name, 'need_classify' => $needClassify, 'i_need' => $iNeed];
            }
            $fileData['meatFile']['count'] = $this->_objClassify->where($where)->count();


            // 酒类
            $where = ['type' => 2];
            $wineFilePage = $this->_objClassify->getPageData($where, $beginWine, $offset);
            foreach ($wineFilePage as $key => $item) {
//            $content = $name['content'];
                $title = $item['title'];
                $name = $item['text_name'];
                $needClassify = $item['need_classify'];
                $hasFeedback = ['classify_id' => $name, 'user_id' => $number];
                $iNeed = 0;
                if ($this->_objUserClassify->hasFeedback($hasFeedback)) {
                    $iNeed = 1;
                }
                $fileData['wineFile']['files'][] = ['id' =>$item['id'], 'name' =>$title, 'file' => $name, 'need_classify' => $needClassify, 'i_need' => $iNeed];
            }
            $fileData['wineFile']['count'] = $this->_objClassify->where($where)->count();


            // 奶类
            $where = ['type' => 4];
            $milkFilePage = $this->_objClassify->getPageData($where, $beginMilk, $offset);
            foreach ($milkFilePage as $key => $item) {
//            $content = $name['content'];
                $title = $item['title'];
                $name = $item['text_name'];
                $needClassify = $item['need_classify'];
                $hasFeedback = ['classify_id' => $name, 'user_id' => $number];
                $iNeed = 0;
                if ($this->_objUserClassify->hasFeedback($hasFeedback)) {
                    $iNeed = 1;
                }
                $fileData['milkFile']['files'][] = ['id' =>$item['id'], 'name' =>$title, 'file' => $name, 'need_classify' => $needClassify, 'i_need' => $iNeed];
            }
            $fileData['milkFile']['count'] = $this->_objClassify->where($where)->count();

            // 图标数据
            $data = [
                [
                    'name' => '标记完成',
                    'number' => 0,
                ],
                [
                    'name' => '标记中',
                    'number' => 0,
                ],
                [
                    'name' => '未标记',
                    'number' => 0,
                ],
                [
                    'name' => '用户数量',
                    'number' => 0,
                ],
            ];

            $dataResult = $this->_objClassify->getIconData();
            foreach ($data as $key => &$info) {
                $info['number'] = $dataResult[$key];
            }
            $this->assign('iconInfo', $data);
            $this->assign('arrData', $fileData);
            $this->assign('page', $page);
        }
        return $this->fetch();
    }

    // 用户标记界面
    public function user()
    {
        $number = Session::get('userNumber');
        $labelArr = $this->_objClassify->getLabelArr($number);
        if (empty($labelArr) || count($labelArr) != 5) {
            $this->success('恭喜你以完成所有标记任务，听首歌休息下吧 ^_^', '/index/player/video');
        }else {
            foreach ($labelArr as $key => &$value) {
                $value['title'] = mb_substr($value['title'], 0, 13, 'UTF-8');
            }
            $this->assign('labelArr', $labelArr);
        }
        return $this->fetch();
    }

    // 搜索详情页
    public function search()
    {
        if ($this->request->isGet()) {
            return $this->fetch();
        }else {
            return $this->fetch();
        }
    }

    // 用户标记界面提交反馈
    public function userLabel()
    {
        $belong = [
        1 => 'belong_food',
        2 => 'belong_wine',
        3 => 'belong_meat',
        4 => 'belong_milk',
        5 => 'belong_others'
    ];
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $data = [];
            $where = [];
            $belongWhat = [];
            for ($i = 1; $i <= 5; $i++) {
                $data[] = ['user_id' => intval($post["number{$i}"]), 'classify_id' => $post["text_name{$i}"], 'suggest' => $post["suggest{$i}"], 'type' => intval($post["type{$i}"])];
                $where[] = ['text_name' => $post["text_name{$i}"]];
                $belongWhat[] = $belong[intval($post["type{$i}"])];
            }
            $this->_objClassify->feedbackArr($where, $data, $belongWhat);
            $this->success('标注成功，感谢你的标注，我们会尽快更新标注信息', '/index/label/user');
        }else {
            $this->error('错误的参数访问，正在返回。');
        }
    }
    // 打开文本
    public function readFile()
    {
        $arrType = [
            1 => '食品数据',
            2 => '酒类',
            3 => '肉类',
            4 => '奶类',
        ];
        $file = Request::instance()->get('name');
        $type = intval(Request::instance()->get('type'));

        $where = ['text_name' => $file, 'type' => $type];

        $content = $this->_objClassify->getContent($where);

        $this->assign('type', $arrType[$type]);
        $this->assign('filename', $file);
        $title = $content['title'];
        $this->assign('title', mb_substr($title, 0, 30, 'utf-8'));
        $this->assign('content', $content['content']);
        return $this->fetch();
    }

    // 仅打开文本
    public function onlyReadFile()
    {
        $arrType = [
            1 => '食品数据',
            2 => '酒类',
            3 => '肉类',
            4 => '奶类',
        ];
        $file = Request::instance()->get('name');
        $type = intval(Request::instance()->get('type'));

        $where = ['text_name' => $file, 'type' => $type];

        $content = $this->_objClassify->getContent($where);

        $this->assign('type', $arrType[$type]);
        $this->assign('filename', $file);
        $title = $content['title'];
        $this->assign('title', mb_substr($title, 0, 30, 'utf-8'));
        $this->assign('content', $content['content']);
        return $this->fetch();
    }

    // feedback
    public function feedback()
    {
        $belong = [
            1 => 'belong_food',
            2 => 'belong_wine',
            3 => 'belong_meat',
            4 => 'belong_milk',
            5 => 'belong_others'
        ];
        $post = $this->request->post();
        $data = ['user_id' => intval($post['number']), 'classify_id' => $post['text_name'], 'suggest' => $post['suggest'], 'type' => intval($post['type'])];
        $where = ['text_name' => $post['text_name']];
        $belongWhat = intval($post['type']);
        $this->_objClassify->feedback($where, $data, $belong[$belongWhat]);
        $this->success('标注成功，感谢你的标注，我们会尽快更新标注信息', '/index/label');
    }

    // 删除文本
    public function delFile()
    {
        exit;
        $dir = CLASSIFICATION_PATH. 'tichu/';
        $file = scandir($dir);
//        print_r($file);
        unset($file[0]); unset($file[1]);
        foreach ($file as $value) {
            $data = file_get_contents($dir. $value);
            if (strlen($data) > 100) {
                file_put_contents(CLASSIFICATION_PATH. 'canuse/'. $value, $data);
            }
        }
        return 'OK';
    }

    // 统计分类准确率
    public function classificationResult()
    {
        $dirN = 'D:\\Ml\\svm\\ClassificationForThreeData\\readyClassify\\酒类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = 'D:\\Ml\\svm\\ClassificationForThreeData\\ClassificationFile\\酒类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['酒类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = 'D:\\Ml\\svm\\ClassificationForThreeData\\readyClassify\\奶类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = 'D:\\Ml\\svm\\ClassificationForThreeData\\ClassificationFile\\奶类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['奶类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = 'D:\\Ml\\svm\\ClassificationForThreeData\\readyClassify\\肉类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = 'D:\\Ml\\svm\\ClassificationForThreeData\\ClassificationFile\\肉类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['肉类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = 'D:\\Ml\\svm\\ClassificationForThreeData\\readyClassify\\食品\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = 'D:\\Ml\\svm\\ClassificationForThreeData\\ClassificationFile\\食品\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['食品准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';
        print_r($result);
    }

    // read text and write mysql
    public function readTextWriteSql()
    {
        exit;
        set_time_limit(0);
        // 食品
        /*$dir = CLASSIFICATION_PATH. 'shipin/';
        $foodFile = scandir($dir);
        unset($foodFile[0]); unset($foodFile[1]);
        foreach ($foodFile as $key => $name) {
            $content = $this->unicodeDecode(file_get_contents($dir. $name));
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData[] = ['text_name'=> $name, 'text_url' => 'www.cnfood.com', 'come_from' => 'www.cnfood.com', 'title' =>$title, 'content' => $content, 'type' => 1, 'need_classify' => 1];
        }

        if (!empty($fileData)) {
            $this->_objClassify->saveAllFile($fileData);
            return 'OK';
        } else {
            return 'false';
        }
        exit;
        */

        // 肉类
        /*$dir = CLASSIFICATION_PATH. 'roulei/';
        $meatFile = scandir($dir);
        unset($meatFile[0], $meatFile[1]);
        $meatFileD = array_slice($meatFile, 4800);
        foreach ($meatFileD as $key => $name) {
            $content = $this->unicodeDecode(file_get_contents($dir. $name));
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData[] = ['text_name'=> $name, 'text_url' => 'search.tech-food.com', 'come_from' => 'search.tech-food.com', 'title' =>$title, 'content' => $content, 'type' => 3, 'need_classify' => 1];
        }
        if (!empty($fileData)) {
            $this->_objClassify->saveAllFile($fileData);
            return 'OK';
        } else {
            return 'false';
        }
        exit;*/
        /*// 酒类
        $dir = CLASSIFICATION_PATH. 'jiulei/';
        $wineFile = scandir($dir);
        unset($wineFile[0], $wineFile[1]);
        foreach ($wineFile as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData[] = ['text_name'=> $name, 'text_url' => 'search.tech-food.com', 'come_from' => 'search.tech-food.com', 'title' =>$title, 'content' => $content, 'type' => 2, 'need_classify' => 1];
        }*/

        // 奶类
        $dir = CLASSIFICATION_PATH. 'nailei/';
        $milkFile = scandir($dir);
        unset($milkFile[0], $milkFile[1]);
        foreach ($milkFile as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData[] = ['text_name'=> $name, 'text_url' => 'search.tech-food.com', 'come_from' => 'search.tech-food.com', 'title' =>$title, 'content' => $content, 'type' => 4, 'need_classify' => 1];
        }
        if (!empty($fileData)) {
            $this->_objClassify->saveAllFile($fileData);
            return 'OK';
        } else {
            return 'false';
        }
    }

    // 建议
    public function suggest()
    {
        return $this->fetch();
    }

    // 退出
    public function signOut()
    {
        Session::clear();
        $this->redirect('/');
    }
}
