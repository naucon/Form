<?php

namespace Naucon\Form\Tests\Security;

use Naucon\Form\Security\SynchronizerTokenBridge;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class SynchronizerTokenBridgeTest extends TestCase
{
    public function testGetToken()
    {
        $synchronizerTokenBridge = $this->createTokenBridge();
        $firstToken = $synchronizerTokenBridge->getToken('test');
        $this->assertNotEmpty($firstToken);
    }

    public function testGenerateToken()
    {
        $synchronizerTokenBridge = $this->createTokenBridge();
        $firstToken = $synchronizerTokenBridge->generateToken('test');
        $secondToken = $synchronizerTokenBridge->generateToken('test');
        $this->assertNotEmpty($firstToken);
        $this->assertNotEmpty($secondToken);
    }

    public function testValidate()
    {
        $synchronizerTokenBridge = $this->createTokenBridge();
        $this->assertFalse($synchronizerTokenBridge->validate('test', ''));
        $token = $synchronizerTokenBridge->getToken('test');
        $this->assertFalse($synchronizerTokenBridge->validate('test', ''));
        $this->assertTrue($synchronizerTokenBridge->validate('test', $token));
    }

    /**
     * @return SynchronizerTokenBridge
     */
    protected function createTokenBridge()
    {
        return new SynchronizerTokenBridge(new CsrfTokenManager(null, new ArrayTokenStorage()));
    }
}
