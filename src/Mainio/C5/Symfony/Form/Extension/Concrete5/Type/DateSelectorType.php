<?php
namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateSelectorType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dh = \Core::make('helper/date');
        $tz = $dh->getTimezone('user')->getName();
        $apptz = $dh->getTimezone('system')->getName();

        $dtTransformer = new DateTimeToStringTransformer(
            $apptz,
            $tz,
            'Y-m-d'
        );
        $builder->addViewTransformer($dtTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dh = \Core::make('helper/form/date_time');
        $view->vars = array_replace($view->vars, array(
            'selector' => $dh->date($view->vars["full_name"], $view->vars["value"]),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date_selector';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            // Defines whether the expected value passed back to the object
            // is expected to be an array (i.e. the output fields have
            // multiple fields in them).
            'compound' => false,
        ));
    }

}
