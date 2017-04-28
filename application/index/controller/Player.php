<?php
namespace app\index\controller;

use think\Controller;

class Player extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function music()
    {
        return $this->fetch();
    }
    public function video()
    {
        return $this->fetch();
    }
}
