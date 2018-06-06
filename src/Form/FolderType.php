<?php

namespace App\Form;

use App\Entity\Folder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', StringType::class)
            ->add('folders', CollectionType::class, array(
                    'entry_type' => FolderType::class,
                    'entry_options' => array()
            ))
            ->add('files', CollectionType::class, array(
                    'entry_type' => FileType::class,
                    'entry_options' => array()
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Schedule'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Folder::class,
        ]);
    }
}
