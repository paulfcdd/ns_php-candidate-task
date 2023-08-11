<?php

declare(strict_types=1);

namespace App\Form;

use App\Repository\NetworkRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CountrySelectType extends AbstractType
{
    public function __construct(private readonly NetworkRepository $networkRepository)
    {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countries = $this->networkRepository->findDistinctCountries();

        $builder
            ->add('country', ChoiceType::class, [
                'choices' => array_combine($countries, $countries),
                'placeholder' => 'Choose a country',
                'required' => true,
            ])
            ->add('select', SubmitType::class)
        ;
    }
}
