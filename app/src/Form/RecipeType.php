<?php
/**
 * Recipe type.
 */

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

/**
 * Class RecipeType.
 */
class RecipeType extends AbstractType
{
    /**
     * Tags data transformer.
     *
     * @var TagsDataTransformer
     */
    private $tagsDataTransformer;

    /**
     * RecipeType constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(TagsDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'Title',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => function ($category) {
                    return $category->getName();
                },
                'label' => 'Category',
                'placeholder' => 'label_none',
                'required' => true,
            ]
        );

        $builder->add(
            'difficulty',
            TextType::class,
            [
                'label' => 'difficulty',
                'required' => false,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'calories',
            NumberType::class,
            [
                'label' => 'Calories',
                'required' => false,
            ]
        );

        $builder->add(
            'time',
            TimeType::class,
            [
                'label' => 'Time',
                'input'  => 'datetime',
                'widget' => 'choice',
            ]
        );

        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'Tagi',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );

        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'Tagi',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );

        $builder->add(
            'content',
            CKEditorType::class,
            [
                'label' => 'Content',
                'input_sync' => true,
                'required' => false,
            ]
        );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Recipe::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'recipe';
    }
}
