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

    protected function client()
    {
        $client = Mockery::mock(\GuzzleHttp\Client::class);
        $client->shouldReceive('get')->withAnyArgs()->andReturnUsing(function ($url) {
            if (str_contains($url, 'getapplist')) {
                $body = file_get_contents(__DIR__ . '/../get_app_list.json');
            }

            return new Response(body: $body);
        });
        return $client;
    }
}
