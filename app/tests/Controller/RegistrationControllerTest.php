<?php
/**
 * Registration controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AlbumControllerTest.
 */
class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/register';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }


    /**
     * Test register action.
     *
     * @throws ContainerExceptionInterface
     */
    public function testRegister(): void
    {
        // given
        $expectedStatusCode = 302;
        $expectedRouteName = 'home';
        $expectedEmail = 'register_user@example.com';

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $this->httpClient->submitForm(
            'Register',
            [
                'registration' => [
                    'email' => $expectedEmail,
                    'password' => 'p@55w0rd',
                ],
            ]
        );

        // then
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail($expectedEmail);
        $this->assertNotNull($user);
    }
}