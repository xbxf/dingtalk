<?php

namespace Xbxf\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Robot
{
    private $webhook = 'https://oapi.dingtalk.com/robot/send?access_token=%s&timestamp=%s&sign=%s';

    private $secret;

    private $accessToken;

    public function __construct()
    {
        $config = config('dingtalk.robot');
        $this->secret = $config['secret'];
        $this->accessToken = $config['access_token'];
    }

    public function send($msg)
    {
        $timestamp = time() . '000';
        $data = $timestamp . "\n" . $this->secret;
        $sign = hash_hmac('sha256', $data, $this->secret, true);
        $sign = utf8_encode(urlencode(base64_encode($sign)));
        $url = sprintf($this->webhook, $this->accessToken, $timestamp, $sign);
        $client = new Client();
        try {
            $resJson = $client->request('POST', $url, [
                'json' => ['msgtype' => 'text', 'text' => ['content' => $msg]]
            ])->getBody()->getContents();
            $resArr = json_decode($resJson, true);
            if ($resArr['errcode'] != 0 || $resArr['errmsg'] != 'ok') {
                Log::info('DingTalk send error', $resArr);
            }
        } catch (GuzzleException $e) {
            Log::error('DingTalk send error', [$e->getMessage()]);
        }
    }
}
