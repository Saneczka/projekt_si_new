<?php
/**
 * AlbumFormType
 */

namespace App\Form;

use App\Entity\Album;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EventFormType
 */
class AlbumFormType extends AbstractType
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
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'album_name',
                    'required' => true,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'album_description',
                    'required' => true,
                ]
            )
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $album = $event->getData();
            $form = $event->getForm();

            // checks if the Album object is edited
            if ($album && null !== $album->getId()) {
                $form->add(
                    'cover',
                    EntityType::class,
                    [
                        'class' => Image::class,
                        'choice_label' => 'title',
                        'label' => 'album_cover',
                        'required' => false,
                        'query_builder' => function (ImageRepository $imageRepository) use ($album) {
                            return $imageRepository->createQueryBuilder('image')
                                ->where('image.album = :id')->setParameter('id', $album->getId());
                        }
                    ]
                );
            }
        });
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class
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
        return 'album';
    }
}
