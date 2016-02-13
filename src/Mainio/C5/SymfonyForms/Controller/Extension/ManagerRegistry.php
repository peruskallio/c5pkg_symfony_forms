<?php
namespace Mainio\C5\SymfonyForms\Controller\Extension;

use Core;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use ORM;

class ManagerRegistry extends AbstractManagerRegistry
{
    protected $container;

    protected function getService($name)
    {
        if ($name == 'em') {
            return ORM::entityManager();
        }
        return Core::make($name);
    }

    protected function resetService($name)
    {
        $app = Core::getFacadeApplication();
        unset($app[$name]);
    }

    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException('Namespace aliases not supported.');
    }

}
