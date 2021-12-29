<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace UMeng\Track;

use GuzzleHttp;
use UMeng\Track\Exception\TokenExpiredException;

class Client
{
    protected string $baseUri = 'https://apptrack.umeng.com';

    protected string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36';

    /**
     * @param string $token Cookies 中的 ap_ckid
     */
    public function __construct(protected string $token)
    {
    }

    /**
     * array(11) {
     * ["appid"]=>
     *    string(5) "55321"
     * ["app_name"]=>
     *    string(12) "月食安卓"  //应用名称
     * ["os_type"]=>
     *    string(1) "1"
     * ["app_key"]=>
     *    string(24) "5d11c1d3570df36eae0008d8"
     * ["add_date"]=>
     *    string(10) "2019-08-21"
     * ["app_type"]=>
     *    string(1) "2" //1 安卓、2 IOS
     * ["is_auth"]=>
     *    int(1)
     * ["role_type"]=>
     *    string(1) "2"
     * ["app_auth_type"]=>
     *    int(0)
     * ["is_chan_user"]=>
     *    int(0)
     * ["os_name"]=>
     *    string(7) "Android" //系统平台 os_name IOS、Android、
     * }.
     */
    /**
     * @return [
     *     'appid' => 1,
     *     'app_name' => '',
     * ]
     */
    public function getAppList(): array
    {
        $response = $this->client()
            ->get('index.php?c=apps&a=getapplist&page_num=1&limit=500');

        $body = (string) $response->getBody();

        try {
            $result = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new TokenExpiredException();
        }

        return $result['ext']['list'] ?? [];
    }

    protected function client(): GuzzleHttp\Client
    {
        $config = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'cookie' => 'ap_ckid=' . $this->token,
                'user-agent' => $this->userAgent,
            ],
        ];
        return new GuzzleHttp\Client($config);
    }
}
