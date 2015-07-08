<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5;

use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class Concrete5Extension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new Type\FileSelectorType(),
            new Type\FormActionsType(),
            new Type\PageSelectorType(),
            new Type\UserSelectorType(),
            new Type\DateSelectorType(),
            new Type\DateTimeSelectorType(),
        );
    }
}
