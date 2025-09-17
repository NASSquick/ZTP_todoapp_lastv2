<?php

/**
 * Comments type.
 */

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class CommentsType.
 *
 * Defines the form used for creating and editing Comment entities.
 * Adds validation so fields cannot be empty or whitespace-only.
 */
class CommentsType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'nick',
            TextType::class,
            [
                'label' => 'label_nick',
                'required' => true,
                'trim' => true,
                'empty_data' => '',
                'attr' => ['maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nickname is required.',
                        'normalizer' => 'trim',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => 'Nickname must be at least {{ limit }} character.',
                        'maxMessage' => 'Nickname cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ]
        );

        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'label_email',
                'required' => true,
                'trim' => true,
                'empty_data' => '',
                'attr' => ['maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email is required.',
                        'normalizer' => 'trim',
                    ]),
                ],
            ]
        );

        $builder->add(
            'text',
            TextareaType::class,
            [
                'label' => 'label_text',
                'required' => true,
                'trim' => true,
                'empty_data' => '',
                'attr' => ['maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Comment text is required.',
                        'normalizer' => 'trim',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => 'Comment must be at least {{ limit }} character.',
                        'maxMessage' => 'Comment cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ]
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'required' => true,
        ]);
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
        return 'comments';
    }
}
