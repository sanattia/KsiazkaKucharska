<?php
/**
 * Category controller tests.
 */

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test index route for index
     */
    public function testIndexRoute(): void
    {
        $expectedStatusCode = 200;

        // when
        $this->httpClient->request('GET', '/category/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }



}