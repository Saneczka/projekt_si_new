<?php
/**
 * ImageFormType
 */

namespace App\Form;

use App\Entity\Album;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EventFormType
 */
class ImageFormType extends AbstractType
{
    /**
     * Builds the form.
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array                                        $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fileConstraints = [
            new File([
                'maxSize' => '10240k',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ],
                'mimeTypesMessage' => 'label_upload_a_valid_photo',
            ]),
        ];

        if($options['photo_required']) {
            $fileConstraints[] = new NotBlank();
        }

        $builder
            ->add('file', FileType::class, [
                'label' => 'label_add_file',
                'mapped' => false,
                'required' => false,
                'constraints' => $fileConstraints,
            ])
            ->add(
                'album',
                EntityType::class,
                [
                    'class' => Album::class,
                    'choice_label' => 'name',
                    'label' => 'image_album',
                    'required' => true,
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'image_title',
                    'required' => true,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'image_description',
                    'required' => true,
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'photo_required' => false,
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'image';
    }
}
