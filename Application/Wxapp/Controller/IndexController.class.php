<?php
namespace Wxapp\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        //1.get code
        $code = $_GET['code'];

        // get access_token
        $accesstokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('appId').'&secret='.C('appSecret').'&code='.$code.'&grant_type=authorization_code';
        $accesstokenArray = json_decode($this->curlGet($accesstokenUrl),true);
        $access_token = $accesstokenArray['access_token'];  //网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
        $openid = $accesstokenArray['openid'];      //用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
        //$expires_in = $accesstokenArray['expires_in'];  //access_token接口调用凭证超时时间，单位（秒）

        // get user_info
        $userinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $userinfoArray = json_decode($this->curlGet($userinfoUrl),true);
        // 查询是否存在用户信息，有则更新，没则添加
        $openid = $userinfoArray['openid'];
        $user = D('Scanuser')->findScanuser($openid);
        if($user) {
            $updateUser = D('Scanuser')->updateUser($user['user_id'],$userinfoArray);
        }else {
            $user = D('Scanuser')->addScanuser($userinfoArray);
        }
        //连接JS_SDK
        $userinfoDBArray = D('Scanuser')->findScanuser($openid);
        $signPackage = D('Wxsign')->getSignPackage(C('appId'),C('appSecret'));
        $this->assign(array(
            'signPackage' => $signPackage,
            'userInfo' => $userinfoDBArray,
        ));
        $this->display();
    }

    public function enrol() {
        $enrol_id = $_GET['enrol_id'];
        $enrol = D('Enrol')->getEnrolByEnrolID($enrol_id);

		if(!$enrol) {
			$this->assign(array(
			    'status' => 0,
                'message' => '信息有误',
                'more_message' => '信息查找失败'
            ));
		}else {
		    if($enrol['sign_time'] != 0) {
                $this->assign(array(
                    'status' => 0,
                    'message' => '不可重复签到！',
                    'more_message' => '该选手已签到',
                ));
            }else {
                $tasks = D('Tasks')->getOneTasksByID($enrol['tasks_id']);
                $enrol_value = D('Enrolvalue')->selectEnrolByEnrolID($enrol_id);
                $project = D('Project')->getOneProjectByID($enrol['project_id']);
                $this->assign(array(
                    'status' => 1,
                    'enrol' => $enrol,
                    'tasks' => $tasks,
                    'enrol_value' => $enrol_value,
                    'project' => $project,
                ));
            }

        }

        $this->display();
    }

    public function signIN() {
        $enrol_id = $_GET['enrol_id'];
        if(!$enrol_id) {
            $this->assign(array(
                'status' => 0,
                'message' => '报名ID不存在！'
            ));
        }else {
            $enrol = D('enrol')->getEnrolByEnrolID($enrol_id);
            if($enrol['sign_time'] != 0) {
                $this->assign(array(
                    'status' => 2,
                    'message' => '不可重复签到！',
                    'enrol' => $enrol,
                ));
            }else {
                $enrolSignIN = D('Enrol')->setEnrolSignTime($enrol_id);
                if($enrolSignIN) {
                    $this->assign(array(
                        'status' => 1,
                        'message' => '签到成功！'
                    ));
                }else {
                    $this->assign(array(
                        'status' => 0,
                        'message' => '签到失败！'
                    ));
                }
            }

        }
        $this->display();
    }

    public function curlGet($url) {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        return $output;
    }
}