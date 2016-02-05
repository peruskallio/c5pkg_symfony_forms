<?php

namespace Mainio\C5\SymfonyForms\Controller\Extension;

use Config;
use Controller;
use Core;
use Twig_Environment;

/**
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
trait SymfonyFormsExtension
{

    protected $twig;
    protected $formFactory;

    public function getTwigEnvironment()
    {
        if (!isset($this->twig)) {
            $prefix = '';
            $page = $this->getPageObject();
            if (strlen($page->getPackageID()) && $page->getPackageID() > 0) {
                $prefix = $page->getPackageHandle() . '/';
            }
            $this->twig = Core::make($prefix . 'environment/twig');

            if (is_object($this->twig)) {
                // Add the path to the Twig environment's loader that contains
                // the form element block extensions.
                $dir = __DIR__;
                for ($i=0; $i < 3; $i++) {
                    $dir = dirname($dir);
                }
                $path = $dir . '/Symfony/Form/Resources/views/Form';
                $loader = $this->twig->getLoader();
                $loader->addPath($path);
            } else {
                $this->twig = false;
            }
        }
        return $this->twig;
    }

    public function getFormFactory()
    {
        if (!isset($this->formFactory)) {
            $twig = $this->getTwigEnvironment();
            if (is_object($twig)) {
                $this->formFactory = $this->createFormFactory($twig);
            } else {
                $this->formFactory = false;
            }
        }
        return is_object($this->formFactory) ? $this->formFactory : null;
    }

    protected function createFormFactory(Twig_Environment $twig)
    {
        $secret = md5(Config::get('concrete.misc.access_entity_updated') . Config::get('concrete.version_installed') . __FILE__);
        $csrfProvider = new \Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider($secret);

        $formEngine = new \Symfony\Bridge\Twig\Form\TwigRendererEngine(array('form_concrete_layout.html.twig'));
        $formEngine->setEnvironment($twig);
        $twig->addExtension(new \Symfony\Bridge\Twig\Extension\FormExtension(
            new \Symfony\Bridge\Twig\Form\TwigRenderer($formEngine, $csrfProvider))
        );

        // Set up the Validator component
        $validator = \Symfony\Component\Validator\Validation::createValidator();

        return \Symfony\Component\Form\Forms::createFormFactoryBuilder()
            ->addExtension(new \Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension())
            ->addExtension(new \Symfony\Component\Form\Extension\Csrf\CsrfExtension($csrfProvider))
            ->addExtension(new \Symfony\Component\Form\Extension\Validator\ValidatorExtension($validator))
            ->addExtension(new \Mainio\C5\Symfony\Form\Extension\Concrete5\Concrete5Extension())
            ->getFormFactory();
    }

}
