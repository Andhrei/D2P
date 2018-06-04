<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recurrence', ChoiceType::class, array(
                'choices' => array(
                    'Daily' => 'DAILY',
                    'Weekly' => 'WEEKLY'
                )
            ))
            ->add('datalist', DatalistType::class, array(
                    'clients' => $options['clients'],
                    'devices' => $options['devices'],
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Create Schedule'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('clients');
        $resolver->setDefined('devices');
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
