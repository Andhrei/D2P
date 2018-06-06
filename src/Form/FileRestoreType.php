<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileRestoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', EntityType::class, array(
            'class' => File::class,
            'choices' => $options['files'],
            'choice_label' => 'path'
        ))
            ->add('save', SubmitType::class, array('label' => 'Restore a file'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('files');
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
