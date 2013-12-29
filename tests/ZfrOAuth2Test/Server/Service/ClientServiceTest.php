<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfrOAuth2Test\Server\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ZfrOAuth2\Server\Entity\Client;
use ZfrOAuth2\Server\Service\ClientService;

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 * @cover \ZfrOAuth2\Server\Service\ClientService
 */
class ClientServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $clientRepository;

    /**
     * @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeRepository;

    /**
     * @var ClientService
     */
    protected $clientService;

    public function setUp()
    {
        $this->objectManager    = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->clientRepository = $this->getMock('ZfrOAuth2Test\Server\Asset\SelectableObjectRepository');
        $this->scopeRepository  = $this->getMock('ZfrOAuth2Test\Server\Asset\SelectableObjectRepository');
        $this->clientService    = new ClientService(
            $this->objectManager,
            $this->clientRepository,
            $this->scopeRepository
        );
    }

    public function testCanGetClient()
    {
        $client = new Client();

        $this->clientRepository->expects($this->once())
                               ->method('find')
                               ->with('client_id')
                               ->will($this->returnValue($client));

        $this->assertSame($client, $this->clientService->getToken('client_id'));
    }

    public function scopeProvider()
    {
        return [
            [
                'scope'       => '',
                'list_scopes' => 'read write'
            ],
            [
                'scope'       => 'read',
                'list_scopes' => 'read write'
            ],
            [
                'scope'       => 'read',
                'list_scopes' => 'read write'
            ]
        ];
    }

    /**
     * @dataProvider scopeProvider
     */
    public function testRegisterClient($scope = '')
    {
        $client = new Client();
        $client->setScope($scope);
    }
}
