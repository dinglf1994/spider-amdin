<?php

/**
 * 后台模块公共函数
 */

function isLogin()
{
    if (!\think\Session::has('userNumber') || !\think\Session::has('name')) {
//        echo "<script> alert('请登录');parent.location.href='/'; </script>";
        return false;
    }else {
        return true;
    }

}
