<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2017/9/28
 * Time: 10:31
 */

namespace Wxapp\Model;
use Think\Model;

class WxsignModel extends Model
{
    private $appId;
    private $appSecret;
    Protected $autoCheckFields = false;
    /*public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }*/

    /**
     * 获取连接微信JS_SDK的签名等信息
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public function getSignPackage($appId = '',$appSecret = '') {
        //获取APPID和APPSecret
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        //获取票据，并设置生命周期
        $jsapiTicket = $this->getJsApiTicket();
        // 生成签名的时间戳
        $timestamp = time();
        // 生成签名的随机串
        $nonceStr = $this->createNonceStr();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,  // 必填，公众号的唯一标识
            "nonceStr"  => $nonceStr,  // 必填，生成签名的随机串
            "timestamp" => $timestamp,  // 必填，生成签名的时间戳
            "url"       => $url,
            "signature" => $signature,  // 必填，签名，见附录1
            "rawString" => $string,
            "jsapiTicket" => $jsapiTicket,
        );
        return $signPackage;
    }

    /**
     * 获取票据，并设置生命周期
     * @return mixed
     */
    private function getJsApiTicket() {
        //检查是否存在票据并且时间有效
        //session('access_token','LdAwLRgGSmVseBc4mYAzJmhD_Au5NdhkCllZ4r_aUBvgGxBdvjqKEx0L77QOQTGeqJ9oQcHya17TEyG53kDCiNm6dHxn89TfWiqGANt2wNOyCujWNIe2fAnUCtDY_wMdAHAgAJALSL');
        //session('jsapi_ticket','kgt8ON7yVITDhtdwci0qeeggkeYwxBTXPYUR9MUPu6VlanbnduJvttUIAX8BGjsNYkwm-CMdFel8WQ-qiH6j-g');
        if(session('jsapi_ticket_expire_time') > time() && session('jsapi_ticket')){
            $ticket = session('jsapi_ticket');
        }else{
            // 获取access_token
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            // 得到jsapiticket
            $ticket = $res->ticket;
            session('jsapi_ticket',$ticket);
            // 设置生命期
            session('jsapi_ticket_expire_time',time()+7000);
        }
        return $ticket;
    }

    /**
     * 生成签名的随机串
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取AccessToken
     * @return mixed
     */
    private function getAccessToken() {
        // 判断是否过期
        if( session('access_token') && session('expire_access_token') > time()){
            $access_token = session('access_token');
        }else{
            // 过期，重新获取
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            session('access_token',$access_token);
            // 设置生命期
            session('expire_access_token',time() + 7000);
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}