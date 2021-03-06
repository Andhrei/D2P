<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Datalist;
use App\Entity\Device;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('save', SubmitType::class, array('label' => 'Backup'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('clients');
        $resolver->setDefined('devices');
        $resolver->setDefaults([
            'data_class' => Datalist::class,
            'clients' => new ArrayCollection(),
            'devices' => new ArrayCollection(),
        ]);
    }
}
