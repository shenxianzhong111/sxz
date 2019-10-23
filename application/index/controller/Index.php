<?php
namespace app\index\controller;
use think\Request;
use think\captcha\Captcha;
use think\Controller;
use think\Session;

class Index extends Controller
{
    public function index()
    {
//        echo md5(MD5(1245664+'ghhhhfjjj'));
       //var_dump(input()) ;
       //var_dump(Request::instance()->header('accept-language')) ;
       return  $this->fetch();
    }

    public function index1()
        {
        return  $this->fetch();
    }
    public function login(){
//var_dump(input('?yzm'));
//exit;

       if((new Captcha())->check(input('yzm'))){
           return [
               'type'=>'验证码正确'
           ];
       }else{
           return  [
               'type'=>'验证码错误'
           ];
       };
//        return 454;
    }
}
