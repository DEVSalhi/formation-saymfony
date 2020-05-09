<?php

namespace App\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface {

    /**
     * @param $date
     * @return string
     */
    public function transform($date)
    {
        if($date === null)  return '';
        return $date->format('d/m/Y');

    }

    /**
     * @param $frenchDate
     * @return DateTime|false
     */
    public function reverseTransform($frenchDate)
    {
        if($frenchDate === null){
            throw new TransformationFailedException('VOus devez fournir une date');
        }

        $date= DateTime::createFromFormat('d/m/Y',$frenchDate);

        if($date === false){
            throw new TransformationFailedException('la format de date est incorrecte');

        }

        return $date;

    }
}