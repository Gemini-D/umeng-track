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
use GuzzleHttp\Psr7\Response;
use JsonException;
use UMeng\Track\Exception\TokenExpiredException;

class WebClient
{
    protected $baseUri = 'https://web.umeng.com';

    /**
     * @param string $token Cookies 中的 umplus_uc_token
     */
    public function __construct(protected string $token, protected string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36')
    {
    }

    public function client(): GuzzleHttp\Client
    {
        $config = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'cookie' => 'umplus_uc_token=' . $this->token,
                'user-agent' => $this->userAgent,
            ],
        ];
        return new GuzzleHttp\Client($config);
    }

    public function trend(bool $all = false)
    {
        $date = date('Y-m-d', strtotime('-6 day'));
        if ($all) {
            $date = '2021-05-24';
        }
        $response = $this->client()
            ->get('main.php?c=flow&a=trend&ajax=module=summary|module=fluxList_currentPage=1_pageType=90&siteid=1279951129&st=' . $date . '&et=' . date('Y-m-d') . '&_=' . $this->microtime_format());

        return $this->body($response);
    }

    public function page()
    {
        $response = $this->client()
            ->get('main.php?c=cont&a=page&ajax=module=summarysource|module=safeinfo|module=statistics_orderBy=pv_orderType=-1_dataType=source_currentPage=1_pageType=90&siteid=1279951129&st=' . date('Y-m-d') . '&et=' . date('Y-m-d') . '&sourcetype=&condtype=&condname=&condvalue=');

        return $this->body($response);
    }

    protected function body(Response $response)
    {
        $body = (string) $response->getBody();

        try {
            $result = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new TokenExpiredException();
        }

        return $result['data'];
    }

    protected function microtime_format()
    {
        [$usec, $sec] = explode(' ', microtime());

        return (int) ((float) $usec + (float) $sec * 1000);
    }
}
