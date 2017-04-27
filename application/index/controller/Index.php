<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{
    const FOOD_TYPE = 1;
    const WINE_TYPE = 2;
    const MEAT_TYPE = 3;
    const MILK_TYPE = 4;
    public function index()
    {
        return $this->fetch();
    }
    // 读取文本 分析
    public function dirList()
    {
        $pageFood = Request::instance()->get('pd') ? Request::instance()->get('pd') : 1;
        $pageMeat = Request::instance()->get('pt') ? Request::instance()->get('pt') : 1;
        $pageMilk = Request::instance()->get('pk') ? Request::instance()->get('pk') : 1;
        $pageWine = Request::instance()->get('pn') ? Request::instance()->get('pn') : 1;
        $pageFood < 1 ? 1 : $pageFood;
        $beginFood = $pageFood * 10;
        $pageMeat < 1 ? 1 : $pageMeat;
        $beginMeat = $pageMeat * 10;
        $pageMilk < 1 ? 1 : $pageMilk;
        $beginMilk = $pageMilk * 10;
        $pageWine < 1 ? 1 : $pageWine;
        $beginWine = $pageWine * 10;
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
        $foodFilePage = array_slice($foodFile, $beginFood, $offset);
        foreach ($foodFilePage as $key => $name) {
            $fileData['foodFile']['files'][] = ['id' =>$key, 'name' =>$name];
        }
        $fileData['foodFile']['count'] = count($foodFile);

        // 肉类
        $dir = CLASSIFICATION_PATH. 'roulei/';
        $meatFile = scandir($dir);
        unset($meatFile[0], $meatFile[1]);
        $meatFilePage = array_slice($meatFile, $beginMeat, $offset);
        foreach ($meatFilePage as $key => $name) {
            $fileData['meatFile']['files'][] = ['id' =>$key, 'name' => $name];
        }
        $fileData['meatFile']['count'] = count($meatFile);

        // 酒类
        $dir = CLASSIFICATION_PATH. 'jiulei/';
        $wineFile = scandir($dir);
        unset($wineFile[0], $wineFile[1]);
        $wineFilePage = array_slice($wineFile, $beginWine, $offset);
        foreach ($wineFilePage as $key => $name) {
            $fileData['wineFile']['files'][] = ['id' =>$key, 'name' =>$name];
        }
        $fileData['wineFile']['count'] = count($wineFile);

        // 奶类
        $dir = CLASSIFICATION_PATH. 'nailei/';
        $milkFile = scandir($dir);
        unset($milkFile[0], $milkFile[1]);
        $milkFilePage = array_slice($milkFile, $beginMilk, $offset);
        foreach ($milkFilePage as $key => $name) {
            $fileData['milkFile']['files'][] = ['id' =>$key, 'name' =>$name];
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
    private function _delFile()
    {
        $dir = CLASSIFICATION_PATH. 'shipin/';
        $file = scandir($dir);
//        print_r($file);
        unset($file[0]); unset($file[1]);
        foreach ($file as $value) {
            $data = file_get_contents($dir. $value);
            if (strlen($data) > 100) {
                file_put_contents(CLASSIFICATION_PATH. 'pinyin2/'. $value, $data);
            }
        }
    }
}
