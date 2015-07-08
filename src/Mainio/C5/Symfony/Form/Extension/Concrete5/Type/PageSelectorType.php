<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer\PageToIntegerTransformer;

class PageSelectorType extends BaseSelectorType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        if ($em instanceof EntityManager) {
            // There are currently no Entity definitions for the core's Page
            // object. So do not use this before there are because this will
            // otherwise result in Exceptions or incorrect form values.
            $builder->addViewTransformer(new PageToIntegerTransformer($em));
        } else {
            parent::buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $ph = \Core::make('helper/form/page_selector');
        $view->vars = array_replace($view->vars, array(
            'selector' => $ph->selectPage($view->vars["full_name"], $view->vars["value"]),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'page_selector';
    }

}
