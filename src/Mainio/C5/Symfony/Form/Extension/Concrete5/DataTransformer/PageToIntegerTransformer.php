<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Concrete\Core\Page\Page;

/**
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class PageToIntegerTransformer implements DataTransformerInterface
{

    private $entityManager;

    public function __construct(EntityManager $em)
    {
        // To avoid Doctrine exceptions, make sure the EntityManager passed
        // here is always the same in which you manage the entities currently
        // being controlled.
        $this->entityManager = $em;
    }

    /**
     * Converts a concrete5 page object to an integer.
     *
     * @param \Concrete\Core\Page\Page $value The page object value
     * @return int The integer value
     * @throws TransformationFailedException If the given value is not an
     *                                       instance of Concrete\Core\Page\Page.
     */
    public function transform($value)
    {
        if ($value === null || $value == '') {
            return null;
        }
        if (!($value instanceof Page)) {
            throw new TransformationFailedException('Expected an instance of a concrete5 page object.');
        }
        return intval($value->getCollectionID());
    }

    /**
     * Converts an integer to a concrete5 page object.
     *
     * @param int $cID
     * @return mixed The value
     * @throws TransformationFailedException If the given value is not a proper
     *                                       concrete5 page ID.
     */
    public function reverseTransform($cID)
    {
        if (!is_numeric($cID) || $cID == 0) {
            return null;
        }

        $rep = $this->entityManager->getRepository('Concrete\Core\Page\Page');

        $c = $rep->find($cID);
        if (!is_object($c) || $c->isError()) {
            throw new TransformationFailedException('Invalid page ID.');
        }

        return $c;
    }
}
