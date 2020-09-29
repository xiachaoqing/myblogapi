<?php
/*
 * @Description: 7牛配置
 * @Author: xiachaoqing
 * @Date: 2020-09-24 10:47:41
 * @LastEditTime: 2020-09-28 20:52:06
 * @LastEditors: xiachaoqing
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Qiniu_model extends Base_Model
{
    private $accessKey = "X6z1xllLZw5jpzZwyvQnCFBO5yKJItKRvUtQnjxj";
    private $secretKey = "kYIevfna-qoTs7ZC-AKZRfNwZY1nP1LDcV2tu3zJ";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_upload_token($bucket, $withWater, $token)
    {
        if($bucket != 'xcqblog') {
            return fail('bucket 目前只能是 xcqblog', PARAMS_INVALID);
        }
        $id = md5(uniqid().$token).create_id();
        $deadline = time() + 3600;
        $args['scope'] = $bucket;
        $args['deadline'] = $deadline;
        $args['returnBody'] = '{"imgUrl": "http://qh5mjpidq.hb-bkt.clouddn.com/$(key)"}'; // 这里要改成自己的域名或其他，上传成功后是返回这个json数据，url就是图片的地址
        $b = json_encode($args);
        $result = array(
            'token'=> $this->signWithData($b)
        );
        return success($result);
    }

    private function signWithData($data)
    {
        $encodedData = $this->base64_urlSafeEncode($data);
        return $this->sign($encodedData) . ':' . $encodedData;
    }

    private function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    private function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . $this->base64_urlSafeEncode($hmac);
    }
}
