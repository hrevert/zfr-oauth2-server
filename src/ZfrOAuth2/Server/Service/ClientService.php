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

namespace ZfrOAuth2\Server\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use ZfrOAuth2\Server\Entity\Client;
use ZfrOAuth2\Server\Exception\OAuth2Exception;

/**
 * Client service
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class ClientService
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ObjectRepository
     */
    protected $clientRepository;

    /**
     * @var Bcrypt
     */
    protected $bcrypt;

    /**
     * @param ObjectManager    $objectManager
     * @param ObjectRepository $clientRepository
     */
    public function __construct(ObjectManager $objectManager, ObjectRepository $clientRepository)
    {
        $this->objectManager    = $objectManager;
        $this->clientRepository = $clientRepository;
        $this->bcrypt           = new Bcrypt();
    }

    /**
     * Register a new client, and return the client secret before it was encryption
     *
     * Please note that the secret must be really kept secret, as it is used for some grant type to
     * authorize the client
     *
     * @param  Client $client
     * @return string
     */
    public function registerClient(Client $client)
    {
        // If no identifier was specified for the client, generate a unique one
        // @TODO: should we use a Doctrine custom generator name here instead?
        $clientId = $client->getId();
        if (empty($clientId)) {
            $client->setId(uniqid());
        }

        // Finally, we must generate a strong, unique secret, and crypt it before storing it
        $secret = Rand::getString(40);
        $client->setSecret($this->bcrypt->create($secret));

        $this->objectManager->persist($client);
        $this->objectManager->flush();

        return $secret;
    }

    /**
     * Get the client using its id
     *
     * @param  string      $id
     * @return Client|null
     */
    public function getClient($id)
    {
        return $this->clientRepository->findOneBy(['id' => $id]);
    }

    /**
     * Check if the client is valid by checking the secret
     *
     * If $allowPublicClients, this means that we do not need a secret to validate the client
     *
     * @param  Client $client
     * @param  string $secret
     * @param  bool   $allowPublicClients
     * @return bool
     */
    public function isClientValid(Client $client, $secret, $allowPublicClients)
    {
        if ($allowPublicClients) {
            return true;
        }

        return $this->bcrypt->verify($secret, $client->getSecret());
    }
}
