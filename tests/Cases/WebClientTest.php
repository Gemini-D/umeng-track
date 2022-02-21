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

namespace HyperfTest\Cases;

use GuzzleHttp\Psr7\Response;
use Mockery;
use UMeng\Track\Exception\TokenExpiredException;
use UMeng\Track\WebClient;

/**
 * @internal
 * @coversNothing
 */
class WebClientTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testTrend()
    {
        $client = Mockery::mock(WebClient::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->trend();

        $this->assertNotEmpty($list);
        $this->assertSame('0', $list ['summary']['items']['pv']);
    }

    public function testPage()
    {
        $client = Mockery::mock(WebClient::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->page();

        $this->assertNotEmpty ( $list );
        $this->assertSame ( '0', $list['summarysource']['items']['pv']);
    }

    protected function client()
    {
        $client = Mockery::mock(\GuzzleHttp\Client::class);
        $client->shouldReceive('get')->withAnyArgs()->andReturnUsing(function ($url) {
            if (str_contains($url, 'a=trend')) {
                $body = file_get_contents(__DIR__ . '/../trend.json');
            }
            if (str_contains($url, 'a=page')) {
                $body = file_get_contents(__DIR__ . '/../page.json');
            }

            return new Response(body: $body);
        });

        return $client;
    }
}
