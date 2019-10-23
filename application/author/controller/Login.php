<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/9
 * Time: 8:57
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Login extends Controller
{
    function index(){
        return $this->fetch();
    }
    function login($account,$password,$test){

        $regular_account='/^(13|15|17|18|19)[\d]{9}$/';
        $regular_password='/^[0-9a-zA-Z]{2,6}$/';
        $test_account=preg_match($regular_account, $account);
        $test_password=preg_match($regular_password, $password);
        if($test_account&&$test_password){
//            $data= Db::table('User')->where('account',$account)->value('error_time');
//
//            if(count(explode('/',$data))>=6){
//                $time_data=explode('/',$data);
//                foreach ($time_data as $key=>$val){
//                    $date=date('d')-date('d',$val);
//                    if(time()-$val>3600 || $date>=1)
//                        unset($time_data[$key]);
//                }
//                var_dump($time_data);
//                die();


//                return[
//                    'code'=>0,
//                    'data'=>'error:一个小时之内密码错误次数已达上限'.(count(explode('/',$data))).'次，请于明日00:00:00后再试 '
//                ];

//            }
            if($test && $test!==''){
                if(!captcha_check($test)){
                    return[
                        'code'=>0,
                        'data'=>'验证码错误'
                    ];
                };

            }
            $data= Db::table('User')->where('account',$account)->select();
            if($data[0]['account']){
                if($data[0]['key']){
                    $md=md5(MD5($password.$data[0]['key']));
                    $data= Db::table('User')->where('account',$account)->where('password',$md)->select();
                    if($data){
                        if($data[0]['user_type']==1){
                            session('admin',$data[0]);

                            return[
                                'code'=>201,
                                'data'=>'恭喜您管理员'.$account.'登录成功'
                            ];
                        }elseif($data[0]['user_type']==2){
                            session('author',$data);
                            //登录成功将错误登录时间清空，病重新生成key值和更新密码md5
                            $str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                            $len=strlen($str)-1;
                            $randstr='';
                            for($i=0;$i<11;$i++){
                                $num=mt_rand(0,$len);
                                $randstr .= $str[$num];
                            }
                            Db::table('user')->where('account',$account)->setField('key', $randstr);
                            Db::table('user')->where('account',$account)->setField('password', md5(md5($password.$randstr)));
                            Db::table('user')->where('account',$account)->setField('error_time', null);

                            return[
                                'code'=>202,
                                'data'=>'恭喜您作者'.$account.'登录成功'
                            ];
                        }else{
                            return[
                                'code'=>203,
                                'data'=>'没有这类用户'
                            ];
                        }
                    }else{
                        $time=Db::table('User')->where('account',$account)->value('error_time');
                        if($time==null){
                            Db::table('user')->where('account',$account)->setField('error_time', time());
                            return[
                                'code'=>107,
                                'data'=>'error:该账号密码错误'
                            ];
                        }else{
                            $time_data=explode('/',$time);
                            foreach ($time_data as $key=>$val){
                                $date=date('d')-date('d',$val);
                                if(time()-$val>3600 || $date>=1)
                                    unset($time_data[$key]);
                            }
                            $time_data=array_values($time_data);
                            $count=count($time_data);
                            if($count<2){
                                array_push($time_data,time());
                                $data_save=implode('/',$time_data);
                                Db::table('user')->where('account',$account)->setField('error_time', $data_save);
                                return[
                                    'code'=>106,
                                    'data'=>'error:该账号密码错误'
                                ];
                            }elseif ($count>=2 && $count<5){
                                $count+=1;
                                array_push($time_data,time());
                                $data_save=implode('/',$time_data);
                                Db::table('user')->where('account',$account)->setField('error_time', $data_save);
                                return[
                                    'code'=>105,
                                    'data'=>'error:该账号密码错误'.($count).'次，请输入验证码 '
                                ];
                            }elseif($count==5){
                                $count+=1;
                                array_push($time_data,time());
                                $data_save=implode('/',$time_data);
                                Db::table('user')->where('account',$account)->setField('error_time', $data_save);
                                return[
                                    'code'=>104,
                                    'data'=>'error:一个小时之内密码错误次数已达上限'.$count.'次，请于明日00:00:00后再试 '
                                ];
                            }
                        }
                    }
                }else{
                    return[
                        'code'=>103,
                        'data'=>'error:该账号未加密处理'
                    ];
                }
            }else{
                return[
                    'code'=>102,
                    'data'=>'error:账号不存在'
                ];
            }
        }else{
            return[
                'code'=>101,
                'data'=>'error:用户名或密码格式不正确'
            ];
        }
    }

}