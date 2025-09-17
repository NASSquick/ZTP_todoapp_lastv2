<?php

/**
 * Photos type.
 */

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\Photo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class PhotosType.
 *
 * Defines the form used for creating and editing Photo entities.
 * Adds validation so fields cannot be empty or whitespace-only.
 */
class PhotosType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'file',
            FileType::class,
            [
                'mapped' => false,
                'label' => 'label_upload_photo',
                'required' => $options['required'],
                'constraints' => new Image(
                    [
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                        ],
                    ]
                ),
            ]
        );

        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label_title',
                'required' => true,
                'trim' => true,
                'attr' => ['maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Photo title is required.',
                        'normalizer' => 'trim',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 64,
                        'minMessage' => 'Title must be at least {{ limit }} characters.',
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters.',
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
                'attr' => ['maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Photo description is required.',
                        'normalizer' => 'trim',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => 'Description must be at least {{ limit }} characters.',
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ]
        );

        $builder->add(
            'gallery',
            EntityType::class,
            [
                'class' => Gallery::class,
                'choice_label' => fn ($gallery) => $gallery->getTitle(),
                'label' => 'label_gallery',
                'placeholder' => 'label_none',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Gallery selection is required.',
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
            'data_class' => Photo::class,
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
        return 'photos';
    }
}
