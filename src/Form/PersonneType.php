<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Personne;
use App\Entity\Profile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

 
class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('name')
            ->add('age')
            ->add('createdAt')
            ->add('updatedAt')
            ->add(
                child: 'profile', 
                type: EntityType::class, 
                options: [
                    'class' => Profile::class,
                    'required' => false,
                    'expanded' => true,
                    'multiple' => false
                ]
            )
            ->add(
                child: 'hobbies', 
                type: EntityType::class, 
                options: [
                    'class' => Hobby::class,
                    'expanded' => false,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('h')
                            ->orderBy('h.designation', 'ASC');
                    },
                    'choice_label' => 'designation'
                ] 
            )
            ->add('job')
            ->add(
                child: 'photo', 
                type: FileType::class,
                options: [
                    'label' => 'Votre image de profil',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/gif',                            
                            ],
                            'mimeTypesMessage' => 'Veuillez entrer une image valide',
                        ])
                    ], 
                ])
            ->add('editer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Personne::class,
            ]
        );
    }
}
