<?php
namespace app\index\controller;

use app\index\model\Classify;
use app\index\model\User;
use app\index\model\UserClassify;
use think\Controller;
use think\Request;
use think\Session;

class Admin extends Controller
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

        return $this->fetch();
    }

}
