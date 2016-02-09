<?php

namespace Mainio\C5\SymfonyForms\Controller\Extension;

use Config;
use Controller;
use Core;
use Database;
use Twig_Environment;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Application\Src\ManagerRegistry;

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
        $session = Core::make('session');

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($session);
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $formEngine = new \Symfony\Bridge\Twig\Form\TwigRendererEngine(array('form_concrete_layout.html.twig'));
        $formEngine->setEnvironment($twig);
        $twig->addExtension(new \Symfony\Bridge\Twig\Extension\FormExtension(
            new \Symfony\Bridge\Twig\Form\TwigRenderer($formEngine, $csrfManager))
        );

        $mr = new ManagerRegistry(
            'c5_symfony_forms_extension', Database::getConnections(), array('em'), Database::getDefaultConnection(), 'em', '\\Doctrine\\ORM\\Proxy\\Proxy'
        );

        // Set up the Validator component
        $validator = \Symfony\Component\Validator\Validation::createValidator();

        return \Symfony\Component\Form\Forms::createFormFactoryBuilder()
            ->addExtension(new \Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension())
            ->addExtension(new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($mr))
            ->addExtension(new \Symfony\Component\Form\Extension\Csrf\CsrfExtension($csrfManager))
            ->addExtension(new \Symfony\Component\Form\Extension\Validator\ValidatorExtension($validator))
            ->addExtension(new \Mainio\C5\Symfony\Form\Extension\Concrete5\Concrete5Extension())
            ->getFormFactory();
    }

}
