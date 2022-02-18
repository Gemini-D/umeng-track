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
use UMeng\Track\Client;
use UMeng\Track\Exception\TokenExpiredException;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetAppListWithTokenExpired()
    {
        $this->expectException(TokenExpiredException::class);

        $client = new Client('xxx');

        $client->getAppList();
    }

    public function testGetAppList()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->getAppList();

        $this->assertNotEmpty($list);
        $this->assertSame('555', $list[0]['appid']);
    }

    public function testGetPlanList()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->getPlanList('555');

        $this->assertNotEmpty($list);
        $this->assertSame('195', $list[0]['rpid']);
    }

    public function testGetMonitorList()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->getMonitorList('555');

        $this->assertNotEmpty($list);
        $this->assertSame('3331', $list[0]['mid']);
    }

    public function testGetActiveTrEnd()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->getActiveTrend('111', '222');
        var_dump($list);

        $this->assertNotEmpty($list);
        $this->assertSame('2020-02-28', $list[0]['day']);
    }

    public function testTrend()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->trend();
        var_dump($list);

        $this->assertNotEmpty($list);
        $this->assertSame('100%', $list['summary']['items']['pvpro']);
    }

    public function testPage()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->page();

        $this->assertNotEmpty($list);
        $this->assertSame('0', $list['summarysource']['items']['pv']);
    }

    protected function client()
    {
        $client = Mockery::mock(\GuzzleHttp\Client::class);
        $client->shouldReceive('get')->withAnyArgs()->andReturnUsing(function ($url) {
            if (str_contains($url, 'getapplist')) {
                $body = file_get_contents(__DIR__ . '/../get_app_list.json');
            }
            if (str_contains($url, 'getplanlist')) {
                $body = file_get_contents(__DIR__ . '/../get_plan_list.json');
            }
            if (str_contains($url, 'getmonitorlist')) {
                $body = file_get_contents(__DIR__ . '/../get_monitor_list.json');
            }
            if (str_contains($url, 'getactivetrend')) {
                $body = file_get_contents(__DIR__ . '/../get_active_trend.json');
            }
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
