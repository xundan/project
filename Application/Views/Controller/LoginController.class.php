<?php
namespace Views\Controller;

use Think\Controller;

class LoginController extends Controller
{

    //注册页面
    public function register()
    {
        $this->display();
    }


    public function index($channel){
        $this->display();
    }

    public function send_code()
    {
        $phone = I("post.phone");
        $code = "";
        $code .= rand(0, 9);
        $code .= rand(0, 9);
        $code .= rand(0, 9);
        $code .= rand(0, 9);
        sendCode($phone, $code);
        session('randomCode', $code);
        session('phone', $phone);
    }


    //注册表单体检处理页面
    public function register_do()
    {
        if (IS_POST) {

            $code = $_SESSION['randomCode'];
            $phone = $_SESSION['phone'];


            $clients['name'] = I('post.phone');
            $clients['channel']=I('post.channel');
            $clients['phone'] = I('post.phone', '', 'strip_tags');
            $clients['pswd'] = I('post.password', '', 'strip_tags');
            $clients['type'] = I('post.role_name');
            $clients['remark']='normal';
            $input_code = I('post.verification', '', 'strip_tags');


            if ($clients['phone'] == $phone) {
                if ($input_code == $code) {
                    $res = D('ClientsKMW')->add($clients);
//                    dump($clients);
//                    echo 'add result: ' . $res;
                    if ($res) {
                        $result = sentToRemote($clients);
                        if ($result&&$result['data']) {
                            $this->clearSession();
                            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                            $this->success('注册成功，正在前往下载', 'after_success', 3);
//                            dump($result);
                        } else {
                            //更新刚才插入的数据，将remark值更新
                            $register=D('ClientsKMW');
                            $update['remark']='kmw_fail';
                            $register->where('name='.$clients['name'].' AND pswd='.$clients['pswd'])->save($update);
                            //错误页面的默认跳转页面是返回前一页，通常不需要设置
                            $this->error('远程服务失效，请稍后再尝试', 'index?channel='.$clients['channel'], 5);
                        }
                    } else {
                        $this->error('服务失效，请稍后再尝试');
                    }

                } else {
                    $this->error("验证码无效或手机号错误");
                }
            } else {
                $this->error("验证码无效或手机号错误");
            }
        } else {
            $this->error("信息错误");
        }

    }


    public function after_success()
    {
        header("Location: http://www.51kuaimei.com/download?media=8810"); /* 跳转 */
        exit;/* 确保其他php代码不会执行. */
    }

    //清空session
    public function clearSession()
    {
        session('randomCode', null);
        session('phone', null);
    }
}