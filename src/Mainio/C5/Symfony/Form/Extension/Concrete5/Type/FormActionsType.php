<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FormActionsType extends AbstractType
{

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Dummy
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'form_actions';
    }

}
