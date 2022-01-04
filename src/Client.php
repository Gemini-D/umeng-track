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
use JsonException;
use UMeng\Track\Exception\TokenExpiredException;

class Client
{
    protected string $baseUri = 'https://apptrack.umeng.com';

    /**
     * @param string $token Cookies 中的 ap_ckid
     */
    public function __construct(protected string $token, protected string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36')
    {
    }

    public function client(): GuzzleHttp\Client
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

    /**
     * @return [[
     *     'appid' => '1',
     *     'app_name' => '',
     *     'os_type' => '1', // 1安卓 2IOS
     *     'app_key' => '',
     *     'app_type' => '1', // 1安卓 2IOS
     *     'os_name' => '',
     * ]]
     */
    public function getAppList(): array
    {
        $response = $this->client()
            ->get('index.php?c=apps&a=getapplist&page_num=1&limit=500');

        $body = (string) $response->getBody();

        return $this->result($body);
    }

    public function getPlanList(string $appid): array
    {
        $response = $this->client()
            ->get('index.php?c=apps&a=getplanlist&appid=' . $appid . '&page_num=1&limit=20&date_type=0&search=&order_value=-1&order_type=click_pv');

        $body = (string) $response->getBody();

        return $this->result($body);
    }

    public function getMonitorList(string $rpid)
    {
        $response = $this->client()
            ->get('index.php?c=apps&a=getmonitorlist&rpid=' . $rpid . '&page_num=1&limit=200&search=&_=' . $this->microtime_format());
        $body = (string) $response->getBody();

        return $this->result($body);
    }

    public function getActiveTrend(string $rpid, string $mid, int $pageNum = 1)
    {
        $response = $this->client()
            ->get('https://apptrack.umeng.com/index.php?c=appreport&a=getactivetrend&rpid=' . $rpid . '&mid=' . $mid . '&limit=20&page_num=' . $pageNum . '&order_type=day&order_value=-1&st=1970-01-01&et=' . date('Y-m-d', strtotime('-1 day')) . '&_=' . $this->microtime_format());
        $body = (string) $response->getBody();

        return $this->result($body);
    }

    protected function result($body)
    {
        try {
            $result = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new TokenExpiredException();
        }

        return $result['ext']['list'] ?? [];
    }

    protected function microtime_format()
    {
        [$usec, $sec] = explode(' ', microtime());

        return (int) ((float) $usec + (float) $sec * 1000);
    }
}
