<?php
namespace app\index\controller;

use app\index\model\ArticleSearch;
use app\index\model\Classify;
use app\index\model\User;
use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{
    private $_objUser;
    private $_objClassify;
    private $_objArticleSearch;
    const FOOD_TYPE = 1;
    const WINE_TYPE = 2;
    const MEAT_TYPE = 3;
    const MILK_TYPE = 4;
    const OTHERS_TYPE = 5;

    const NORMAL_USER = 1;
    const ADMIN_USER = 2;
    public function __construct(Request $request, User $user, Classify $classify, ArticleSearch $articleSearch)
    {
        parent::__construct($request);
        $this->_objUser = $user;
        $this->_objClassify = $classify;
        $this->_objArticleSearch = $articleSearch;
    }

    public function index()
    {
        return $this->fetch('login');
    }
    public function login()
    {
        $data = $this->request->post();
        $login['number'] = trim($data['number']);
        $login['password'] = md5(trim($data['password']));
        if ($this->_objUser->verify($login) && $name = $this->_objUser->updateLoginState($login['number'])) {
            Session::set('userNumber', $login['number']);
            Session::set('name', $name['name']);
            Session::set('rank', $name['rank']);
            if (self::NORMAL_USER == $name['rank']) {
                $this->success('登陆成功，正在跳转', '/index/label/user');
            } else {
                $this->success('登陆成功，正在跳转', '/index/label');
            }
        }else {
            $this->error('登陆失败。。。');
        }
    }


    // 读取文本 分析
    public function dirList()
    {
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

        // 食品
        $dir = CLASSIFICATION_PATH. 'shipin/';
        $foodFile = scandir($dir);
        unset($foodFile[0]); unset($foodFile[1]);
        $foodFilePage = array_slice($foodFile, $beginFood, $offset, true);
        foreach ($foodFilePage as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData['foodFile']['files'][] = ['id' =>$key, 'name' =>$title, 'file' => $name];
        }
        $fileData['foodFile']['count'] = count($foodFile);

        // 肉类
        $dir = CLASSIFICATION_PATH. 'roulei/';
        $meatFile = scandir($dir);
        unset($meatFile[0], $meatFile[1]);
        $meatFilePage = array_slice($meatFile, $beginMeat, $offset, true);
        foreach ($meatFilePage as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData['meatFile']['files'][] = ['id' =>$key, 'name' => $title, 'file' => $name];
        }
        $fileData['meatFile']['count'] = count($meatFile);

        // 酒类
        $dir = CLASSIFICATION_PATH. 'jiulei/';
        $wineFile = scandir($dir);
        unset($wineFile[0], $wineFile[1]);
        $wineFilePage = array_slice($wineFile, $beginWine, $offset, true);
        foreach ($wineFilePage as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData['wineFile']['files'][] = ['id' =>$key, 'name' =>$title, 'file' => $name];
        }
        $fileData['wineFile']['count'] = count($wineFile);

        // 奶类
        $dir = CLASSIFICATION_PATH. 'nailei/';
        $milkFile = scandir($dir);
        unset($milkFile[0], $milkFile[1]);
        $milkFilePage = array_slice($milkFile, $beginMilk, $offset, true);
        foreach ($milkFilePage as $key => $name) {
            $content = file_get_contents($dir. $name);
            $tip = strpos($content, ',');
            $title =substr($content, 0, $tip);
            $fileData['milkFile']['files'][] = ['id' =>$key, 'name' =>$title, 'file' => $name];
        }
        $fileData['milkFile']['count'] = count($milkFile);

        $this->assign('arrData', $fileData);
        $this->assign('page', $page);
        return $this->fetch();
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

        $content = '';
        switch ($type)
        {
            case self::FOOD_TYPE : $content = file_get_contents(CLASSIFICATION_PATH. 'shipin/'. $file);
                break;
            case self::WINE_TYPE : $content = file_get_contents(CLASSIFICATION_PATH. 'jiulei/'. $file);
                break;
            case self::MEAT_TYPE : $content = file_get_contents(CLASSIFICATION_PATH. 'roulei/'. $file);
                break;
            case self::MILK_TYPE : $content = file_get_contents(CLASSIFICATION_PATH. 'nailei/'. $file);
                break;
        }
        $this->assign('type', $arrType[$type]);
        $this->assign('filename', $file);
        $tip = strpos($content, ',');
        $title =substr($content, 0, $tip);
        $this->assign('title', mb_substr($title, 0, 30, 'utf-8'));
        $this->assign('content', $content);
        return $this->fetch();
    }

    // 删除文本
    public function delFile()
    {
        exit;
        $dir = "D:\\Ml\\search.tech-food.com\\5\\";
        $file = scandir($dir);
//        print_r($file);
        unset($file[0]); unset($file[1]);
        foreach ($file as $value) {
            $data = file_get_contents($dir. $value);
            if (strlen($data) > 100) {
                file_put_contents("D:\\Ml\\search.tech-food.com\\55\\". $value, $data);
            }
        }
        return 'OK';
    }

    // 统计分类准确率2
    public function classificationResultB()
    {
        $dir1 = 'D:\\Ml\\svm\\ClassificationThreeNew\\testData\\ready\\';
        $dir2 = 'D:\\Ml\\svm\\ClassificationThreeNew\\testData\\result\\';
        $dirN = $dir1. '酒类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dir2. '酒类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['酒类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dir1. '奶类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dir2. '奶类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['奶类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dir1. '肉类\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dir2. '肉类\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['肉类准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        print_r($result);
    }
    // 统计分类准确率1
    public function classificationResultA()
    {
        exit;
        $dirBase = 'D:\\Ml\\svm\\ClassificationData\\';
        $dirN = $dirBase. 'readyClassify\\食品\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\食品\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['食品准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';
        print_r($result);exit;
        $dirN = $dirBase. 'readyClassify\\法律\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\法律\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['法律准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\交通运输\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\交通运输\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['交通运输准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\教育\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\教育\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['教育准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\军事\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\军事\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['军事准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\历史\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\历史\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['历史准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\农林\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\农林\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['农林准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\食品\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\食品\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['食品准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\数学\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\数学\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['数学准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\体育\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\体育\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['体育准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\天文科学\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\天文科学\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['天文科学准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\文化\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\文化\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['文化准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\文学\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\文学\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['文学准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\医药卫生\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\医药卫生\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['医药卫生准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\艺术\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\艺术\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['艺术准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\哲学\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\哲学\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['哲学准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\政治法律\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\政治法律\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['政治法律准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        $dirN = $dirBase. 'readyClassify\\自然科学总论\\';
        $dirN = iconv("UTF-8","gb2312",$dirN);
        $fileN = scandir($dirN);
        unset($fileN[0], $fileN[1]);
        $dirNR = $dirBase. 'ClassificationFile\\自然科学总论\\';
        $dirNR = iconv("UTF-8","gb2312",$dirNR);
        $fileNR = scandir($dirNR);
        unset($fileNR[0], $fileNR[1]);
        $result['自然科学总论准确度'] = floatval(count(array_intersect($fileN, $fileNR)) / count($fileN)) * 100 . '%';

        print_r($result);
    }

    // 导出标注结果
    public function exportResult()
    {
        $labelArr = [
            'belong_food' => self::FOOD_TYPE,
            'belong_meat' => self::MEAT_TYPE,
            'belong_milk' => self::MILK_TYPE,
            'belong_wine' => self::WINE_TYPE,
            'belong_others' => self::OTHERS_TYPE,
        ];
        $data = $this->_objClassify->getLabelResult();
        foreach ($data as $key => $item) {
            $arr['belong_food'] = $item['belong_food'];
            $arr['belong_meat'] = $item['belong_meat'];
            $arr['belong_milk'] = $item['belong_milk'];
            $arr['belong_wine'] = $item['belong_wine'];
            $arr['belong_others'] = $item['belong_others'];
            $maxLabel = $this->_getMaxLabel($arr);
            switch ($labelArr[$maxLabel])
            {
                case self::FOOD_TYPE :
                    $str = self::FOOD_TYPE. ",{$item['text_name']}".PHP_EOL;
                    file_put_contents('label_result.txt', $str, FILE_APPEND);
                    break;
                case self::MILK_TYPE :
                    $str = self::MILK_TYPE. ",{$item['text_name']}".PHP_EOL;
                    file_put_contents('label_result.txt', $str, FILE_APPEND);
                    break;
                case self::WINE_TYPE :
                    $str = self::WINE_TYPE. ",{$item['text_name']}".PHP_EOL;
                    file_put_contents('label_result.txt', $str, FILE_APPEND);
                    break;
                case self::MEAT_TYPE :
                    $str = self::MEAT_TYPE. ",{$item['text_name']}".PHP_EOL;
                    file_put_contents('label_result.txt', $str, FILE_APPEND);
                    break;
                case self::OTHERS_TYPE :
                    $str = self::OTHERS_TYPE. ",{$item['text_name']}".PHP_EOL;
                    file_put_contents('label_result.txt', $str, FILE_APPEND);
                    break;
            }
        }

        return "分类结果已经保存";
    }
    private function _getMaxLabel($arr)
    {
        $pos = array_search(max($arr), $arr);
        return $pos;
    }
    // 整理标注结果
    public function updateLabel()
    {
        if ($this->_objClassify->updateLabel()) {
            return "OK";
        }else {
            return "FALSE";
        }
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

    public function readLabelResult()
    {
        $fileArr = [
            2 => '酒类',
            3 => '肉类',
            4 => '奶类',
        ];
        foreach ($fileArr as $k => $v) {
            $dir = CLASSIFICATION_PATH . "{$v}/";
            $dir = iconv("UTF-8", "gb2312", $dir);
            $fileName = scandir($dir);
            unset($fileName[0], $fileName[1]);
            foreach ($fileName as $key => $name) {
                $name = str_replace('.txt', '', $name);
                $this->_objArticleSearch->importLabel($k, $name);
            }
        }
        return '导入分类结果成功';
    }

    // 数据展示页
    public function showYou()
    {
        //获取每类数据的来源网站排名前十的
        $webRank = $this->_objArticleSearch->getWebRank();
        foreach ($webRank as &$value) {
            $value['article_source'] === '-' ? $value['article_source'] = '中国食品网' : '';
        }
//        var_dump($webRank);exit;
        // 获取五年的间的数据分布
        $yearRank = $this->_objArticleSearch->getYearRank();
        $yearRankLimit = [];
        if ($yearRank) {
            $yearRankLimit = array_slice($yearRank, 0, 10, true);
        }
        $this->assign('yearRank', $yearRankLimit);
        $this->assign('rankNum', count($webRank));
        $this->assign('webRank', $webRank);
        return $this->fetch();
    }
}
