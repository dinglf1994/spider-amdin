<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function dirList()
    {
        // 食品
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
