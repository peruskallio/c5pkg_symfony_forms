<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer\UserToIntegerTransformer;

class UserSelectorType extends BaseSelectorType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        if ($em instanceof EntityManager) {
            // There are currently no Entity definitions for the core's User
            // object. So do not use this before there are because this will
            // otherwise result in Exceptions or incorrect form values.
            $builder->addViewTransformer(new UserToIntegerTransformer($em));
        } else {
            parent::buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Through configurations variables we should allow the user to
        //       use also $uh->quickSelect(...) and $uh->selectMultipleUsers(...).
        $uh = \Core::make('helper/form/user_selector');
        $view->vars = array_replace($view->vars, array(
            'selector' => $uh->selectUser($view->vars["full_name"], $view->vars["value"]),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_selector';
    }

}
