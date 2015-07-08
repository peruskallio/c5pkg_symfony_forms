<?php
namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
//use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;
//use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer\DateTimeToFormWidgetTransformer;

class DateTimeSelectorType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dh = \Core::make('helper/date');
        $tz = $dh->getTimezone('user')->getName();
        $apptz = $dh->getTimezone('system')->getName();

        //$name = $builder->getFormConfig()->getName();

        $parts = array('year', 'month', 'day', 'hour', 'minute');
        $dtTransformer = new DateTimeToFormWidgetTransformer(
            $apptz,
            $tz
        );
        $builder->addViewTransformer($dtTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dh = \Core::make('helper/form/date_time');

        $name = $view->vars["full_name"];
        $prefix = substr($name, 0, strlen($name)-1);

        $selector = $dh->datetime($name, $view->vars["value"]);

        // Change the c5 generated form names for something that the Symphony
        // forms understand. Changes e.g. form[date_dt] to form[date][dt].
        $prefix = str_replace(array('[', ']'), array('\[', '\]'), $prefix);
        $pattern = '/name="' . $prefix . '_([^"]+)\]"/';
        $selector = preg_replace($pattern, 'name="' . $name . '[\1]"', $selector);

        $view->vars = array_replace($view->vars, array(
            'selector' => $selector
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date_time_selector';
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
