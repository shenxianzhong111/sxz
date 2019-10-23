<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/9
 * Time: 8:57
 */
namespace app\boss\controller;

use boss\Login;
use think\Db;

class Index extends Login
{
    function index(){
//        if(!session('boss')){
//            $this->success('您尚未登录，请先去登录','admin/login/index');
//        }
        return $this->fetch();
    }
    function register($account){
        $account=substr($account,0,strlen($account)-1);
        $account=explode(',',$account);

        $count=0;
        foreach ($account as $val){
            $data= Db::table('User')->where('account',$val)->value('account');
            if(!$data){
                $str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $len=strlen($str)-1;
                $randstr='';
                for($i=0;$i<11;$i++){
                    $num=mt_rand(0,$len);
                    $randstr .= $str[$num];
                }
                $password=md5(md5('000'.$randstr));
                $data = ['account' =>$val, 'password' =>$password,'key'=>$randstr,'error_time'=>'','user_type'=>2,'photo'=>''];
                Db::table('User')->insert($data);
                $count++;
            }
        }
//        dump($user->account);
        return [
            'success'=>'共注册成功用户'.$count.'位',
            'fail'=>'注册失败'.(count($account)-$count).'位'
        ];
    }

    function watch(){


        return $this->fetch();
    }

}