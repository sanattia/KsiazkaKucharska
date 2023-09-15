<?php
/**
 * Comment Form Type tests.
 */

namespace Form\Type;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class CommentTypeTest.
 */
class CommentTypeTest extends TypeTestCase
{
    /**
     * Test buildForm.
     */
    public function testSubmitValidData(): void
    {
        $formData =
            [
                'content' => 'testowy Content komentarza',
            ];

        $model = new Comment();
        $form = $this->factory->create(CommentType::class, $model);

        $expected = new Comment();
        $expected->setContent($formData['content']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}