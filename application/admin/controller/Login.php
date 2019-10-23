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
    function photo($account){
        $data= Db::table('User')->where('account',$account)->value('photo');
        echo $data;
    }
    function login($account,$password,$test=''){
        $regular_account='/^(13|15|17|18|19)[\d]{9}$/';
        $regular_password='/^[0-9a-zA-Z]{2,6}$/';
        $test_account=preg_match($regular_account, $account);
        $test_password=preg_match($regular_password, $password);
        if($test_account&&$test_password){
            $data= Db::table('User')->where('account',$account)->value('error_time');
            $time_data=explode('/',$data);
            if($time_data[0]!=''){
                foreach ($time_data as $key=>$val){
                    $da=date('Ymd')-date('Ymd',$val);
                    if(time()-$val>3600 || $da>=1)
                        unset($time_data[$key]);
                    //Db::table('user')->where('account',$account)->setField('error_time', implode('/',$time_data));
//                    $data= Db::table('User')->where('account',$account)->value('error_time');
                }
            }
            if(count($time_data)>=6){
                return[
                    'code'=>0,
                    'data'=>'error:一个小时之内密码错误次数已达上限'.(count(explode('/',$data))).'次，请于明日00:00:00后再试 '
                ];
            }
            $key= Db::table('User')->where('account',$account)->value('key');
                if($key){
                    $md=md5(MD5($password.$key));
                    $data= Db::table('User')->where('account',$account)->where('password',$md)->select();
                    if($data){
                        if(count($time_data)>=3){
                                if(!captcha_check($test)&&$test!=''){
                                    return[
                                        'code'=>105,
                                        'data'=>'error:验证码错误，请重新输入验证码 '
                                    ];
                                }elseif ($test==''){
                                    return[
                                        'code'=>105,
                                        'data'=>'error:验证码错误'.(count($time_data)).'次，请输入验证码 '
                                    ];
                                }
                        }
                        $str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        $len=strlen($str)-1;
                        $randstr='';
                        for($i=0;$i<11;$i++){
                            $num=mt_rand(0,$len);
                            $randstr .= $str[$num];
                        }
                        Db::table('User')->where('account',$account)->setField('key', $randstr);
                        Db::table('User')->where('account',$account)->setField('password', md5(md5($password.$randstr)));
                        Db::table('User')->where('account',$account)->setField('error_time', null);

                        if($data[0]['user_type']==1){
                            session('admin',$data[0]);
                            return[
                                'code'=>201,
                                'data'=>'恭喜您管理员'.$account.'登录成功',
                                'account'=>$account,
                                'photo'=>$data[0]['photo']
                            ];
                        }elseif($data[0]['user_type']==2){
                            session('author',$data);
                            return[
                                'code'=>202,
                                'data'=>'恭喜您作者'.$account.'登录成功',
                                'account'=>$account,
                                'photo'=>$data[0]['photo']
                            ];
                        }elseif($data[0]['user_type']==3){
                            session('boss',$data);
                            return[
                                'code'=>203,
                                'data'=>'欢迎大boss',
                                'account'=>$account,
                                'photo'=>$data[0]['photo']
                            ];
                        }else{
                            return[
                                'code'=>204,
                                'data'=>'没有这类用户'
                            ];
                        }
                    }else{
                        //$time=Db::table('User')->where('account',$account)->value('error_time');
                        if($time_data[0]==null){
                            Db::table('User')->where('account',$account)->setField('error_time', time());
                            return[
                                'code'=>107,
                                'data'=>'error:该账号密码错误'
                            ];
                        }else{
                            $count=count($time_data);
                            if($count<2){
                                array_push($time_data,time());
                                Db::table('User')->where('account',$account)->setField('error_time', implode('/',$time_data));
                                return[
                                    'code'=>106,
                                    'data'=>'error:该账号密码错误'
                                ];
                            }elseif ($count>=2 && $count<5){
                                $count+=1;
                                array_push($time_data,time());
                                Db::table('User')->where('account',$account)->setField('error_time', implode('/',$time_data));
                                return[
                                    'code'=>105,
                                    'data'=>'error:该账号密码错误'.($count).'次，重新输入密码并输入验证码 '
                                ];
                            }elseif($count==5){
                                $count+=1;
                                array_push($time_data,time());
                                Db::table('User')->where('account',$account)->setField('error_time', implode('/',$time_data));
                                return[
                                    'code'=>104,
                                    'data'=>'error:一个小时之内密码错误次数已达上限'.$count.'次，请于明日00:00:00后再试 '
                                ];
                            }
                        }
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