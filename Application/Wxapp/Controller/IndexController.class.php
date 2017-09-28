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
        // save in db
        $user = D('Scanuser')->addScanuser($userinfoArray);

        //连接JS_SDK
        $signPackage = D('Wxsign')->getSignPackage(C('appId'),C('appSecret'));
        $this->assign(array(
            'signPackage' => $signPackage,
            'userInfo' => $userinfoArray,
        ));
        $this->display();
    }

    public function enrol() {
        $enrol_id = $_GET['enrol'];
        print_r($enrol_id);
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