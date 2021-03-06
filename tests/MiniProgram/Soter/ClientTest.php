<?php
/**
 * Created by PhpStorm.
 * User: XueSi
 * Email: <1592328848@qq.com>
 * Date: 2021/5/5
 * Time: 21:48
 */

namespace EasySwoole\WeChat\Tests\MiniProgram\Soter;

use EasySwoole\WeChat\Kernel\ServiceContainer;
use EasySwoole\WeChat\Tests\Mock\Message\Status;
use EasySwoole\WeChat\Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use EasySwoole\WeChat\MiniProgram\Soter\Client;

class ClientTest extends TestCase
{
    public function testVerifySignature()
    {
        $response = $this->buildResponse(Status::CODE_OK, $this->readMockResponseJson('verifySignature.json'));

        $app = $this->mockAccessToken(new ServiceContainer([
            'appId' => 'mock_appid',
            'appSecret' => 'mock_secret'
        ]));

        $app = $this->mockHttpClient(function (ServerRequestInterface $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('/cgi-bin/soter/verify_signature', $request->getUri()->getPath());
            $this->assertEquals('access_token=mock_access_token', $request->getUri()->getQuery());
        }, $response, $app);

        $client = new Client($app);

        $this->assertIsArray($client->verifySignature('mock-openid', 'mock-json', 'mock-signature'));

        $this->assertSame(json_decode($this->readMockResponseJson('verifySignature.json'), true), $client->verifySignature('mock-openid', 'mock-json', 'mock-signature'));
    }

    private function readMockResponseJson($filename)
    {
        return file_get_contents(__DIR__ . '/mock_data/' . $filename);
    }
}