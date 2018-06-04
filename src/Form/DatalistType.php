<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Datalist;
use App\Entity\Device;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatalistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, array(
                'class' => Client::class,
                'choices' => $options['clients'],
                'choice_label' => 'hostname'
            ))
            ->add('device', EntityType::class, array(
                'class' => Device::class,
                'choices' => $options['devices'],
                'choice_label' => 'type'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('clients');
        $resolver->setDefined('devices');
        $resolver->setDefaults([
            'data_class' => Datalist::class,
        ]);
    }
}
