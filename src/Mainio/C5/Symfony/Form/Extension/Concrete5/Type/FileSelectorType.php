<?php
namespace Mainio\C5\Symfony\Form\Extension\Concrete5\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Concrete\Core\File\File;
use Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer\FileToIntegerTransformer;

class FileSelectorType extends BaseSelectorType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
        if ($em instanceof EntityManager) {
            $builder->addViewTransformer(new FileToIntegerTransformer($em));
        } else {
            parent::buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Through configurations variables, we should allow the defining
        //       the file type for the selector (e.g. $fm->image(...), $fm->doc(...))
        $fm = \Core::make('helper/concrete/file_manager');
        if (is_object($view->vars["value"])) {
            $bf = $view->vars["value"];
        } elseif (strlen($view->vars["value"])) {
            $bf = File::getByID($view->vars["value"]);
        }
        $view->vars = array_replace($view->vars, array(
            'selector' => $fm->file($view->vars["id"], $view->vars["full_name"], t("Choose File"), $bf),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'file_selector';
    }

}
