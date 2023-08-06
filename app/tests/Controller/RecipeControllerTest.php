<?php

namespace Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RecipeControllerTest extends WebTestCase
{
    /**
     * Test '/recipe' route.
     */
    public function testRecipeRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/recipe');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(301, $resultHttpStatusCode);
    }
}
