<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\Type\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SpotFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('address')
            ->add('description', TextareaType::class, array(
                'required'  => false,
                'attr'      => array('class' => 'tinymce hm_input_text'),
            ))
            ->add('images', CollectionType::class, array(
                'entry_type'    => ImageType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'label'         => false,
                'required'      => false,
            ))
            ->add('genders', EntityType::class, array(
                'class'         => 'AppBundle:Gender',
                'choice_label'  => 'name',
                'multiple'      => true,
                'required'      => false,
            ))
            ->add('locations', EntityType::class, array(
            'class'         => 'AppBundle:Location',
            'choice_label'  => 'name',
            'multiple'      => true,
            'required'      => false,
        ));
    }

    public function getParent()
    {
        return SpotType::class;
    }

}