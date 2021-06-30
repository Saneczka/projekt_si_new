<?php
/**
 * UserDataFormType
 */

namespace App\Form;

use App\Entity\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserDataFormType
 */
class UserDataFormType extends AbstractType
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
                'firstName',
                TextType::class,
                [
                    'label' => 'user_data_firstname',
                    'required' => false,
                    //'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'user_data_lastname',
                    'required' => false,
                    //'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'user_data_email',
                    'required' => false,
                    //'attr' => ['class' => 'form-control'],
                ]
            )
        ;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserData::class
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
        return 'userData';
    }
}
