<?php
/**
 * Category Form Type tests.
 */

namespace Form\Type;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class CategoryTypeTest.
 */
class CategoryTypeTest extends TypeTestCase
{
    /**
     * Test buildForm.
     */
    public function testSubmitValidData(): void
    {
        $formData =
            [
                'name' => 'test',
            ];

        $model = new Category();
        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setName($formData['name']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}