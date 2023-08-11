<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Network;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitySelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('network', EntityType::class, [
                'class' => Network::class,
                'query_builder' => function (EntityRepository $er) use($options) {
                    return $er->createQueryBuilder('n')
                        ->select('n')
                        ->where('n.country = :country')
                        ->setParameter('country', $options['country'])
                        ->orderBy('n.city', 'ASC');
                },
                'choice_label' => function(Network $network) {
                    return sprintf('%s (%s)', $network->getCity(), $network->getCompanyName());
                },
                'placeholder' => 'Choose a network',
                'required' => true,
            ])
            ->add('select', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('country');
    }
}
