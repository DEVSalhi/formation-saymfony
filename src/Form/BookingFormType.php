<?php

namespace App\Form;

use App\Entity\Booking;

use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingFormType extends AbstractType
{
    private $transformer;
    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer=$transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class,['attr'=>['label '=>'date de start']])
            ->add('endDate',TextType::class,['attr'=>['label '=>'date de départ']])
            ->add('comment',TextareaType::class,['attr'=>['label'=>false]])
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // validation groupe on peut les mettre ici ou dans le controlleur
        $resolver->setDefaults([
            'data_class' => Booking::class,['validation_groups'=>["front","default"]]
        ]);
    }
}
