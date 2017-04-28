<?php
namespace app\index\controller;

use app\index\model\User;
use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{
    private $_objUser;
    const FOOD_TYPE = 1;
    const WINE_TYPE = 2;
    const MEAT_TYPE = 3;
    const MILK_TYPE = 4;
    public function __construct(Request $request, User $user)
    {
        parent::__construct($request);
        $this->_objUser = $user;
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
            $this->success('登陆成功，正在跳转', '/index/index/dirlist');
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
}
