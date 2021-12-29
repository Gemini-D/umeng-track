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

use UMeng\Track\Client;
use UMeng\Track\Exception\TokenExpiredException;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends AbstractTestCase
{
    public function testGetAppListWithTokenExpired()
    {
        $this->expectException(TokenExpiredException::class);

        $client = new Client('xxx');

        $client->getAppList();
    }
}
