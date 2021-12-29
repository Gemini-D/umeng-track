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
        $list = $client->getMonitorList('195');

        $this->assertNotEmpty($list);
        $this->assertSame('122', $list[0]['rpid']);
        $this->assertSame('125', $list[0]['mid']);
    }

    public function testGetActiveTrend()
    {
        $client = Mockery::mock(Client::class . '[client]', ['xxx']);
        $client->shouldReceive('client')->andReturn($this->client());
        $list = $client->getActiveTrend('122', '125');

        $this->assertNotEmpty($list);
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

            return new Response(body: $body);
        });
        return $client;
    }
}
