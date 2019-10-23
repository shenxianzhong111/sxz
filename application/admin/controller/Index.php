<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/9
 * Time: 8:57
 */
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    function index(){
        if(!session('admin')){
            $this->success('您尚未登录，请先去登录','login/index');
        }
        return $this->fetch();
    }
    function boss(){
        if(!session('boss')){
            $this->success('您尚未登录，请先去登录','login/index');
        }
        return $this->fetch('boss');
    }
    function demo(){
        return $this->fetch('demo');
    }
}