<?php
/**
 * EditUserTypeTest.
 */

namespace Form\Type;

use App\Form\Type\EditUserType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class EditUserTypeTest.
 */
class EditUserTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(EditUserType::class);

        $formData = [
            'password' => 'new_password123',
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        
        $this->assertEquals($formData, $form->getData());
    }

}
