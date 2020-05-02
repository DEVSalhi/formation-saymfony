<?php

namespace App\Form;


use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{

    /**
     * @param string $label
     * @param string $plcaholder
     * @param string$class
     * @return array
     */

    private function getConfigurationField($label,$plcaholder,$class){

        return ['label'=>$label,
                'attr'=>['placeholder'=>$plcaholder,
                'class'=>$class]];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfigurationField('Titre','tapez svp', 'col-md-6'))

            ->add('price',MoneyType::class)
            ->add('introduction',TextType::class)
            ->add('content',TextareaType::class)
            ->add('coverImage',UrlType::class)
            ->add('rooms',IntegerType::class)
            ->add('images',CollectionType::class,
                ['entry_type'=>ImageType::class,
                    'allow_add'=>true,
                    'allow_delete'=>true
                    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
