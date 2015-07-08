<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Concrete\Core\User\User;

/**
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class UserToIntegerTransformer implements DataTransformerInterface
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
     * Converts a concrete5 user object to an integer.
     *
     * @param \Concrete\Core\User\User $value The user object value
     * @return int The integer value
     * @throws TransformationFailedException If the given value is not an
     *                                       instance of Concrete\Core\User\User.
     */
    public function transform($value)
    {
        if ($value === null || $value == '') {
            return null;
        }
        if (!($value instanceof User)) {
            throw new TransformationFailedException('Expected an instance of a concrete5 user object.');
        }
        return intval($value->getUserID());
    }

    /**
     * Converts an integer to a concrete5 page object.
     *
     * @param int $uID
     * @return mixed The value
     * @throws TransformationFailedException If the given value is not a proper
     *                                       concrete5 page ID.
     */
    public function reverseTransform($uID)
    {
        if (!is_numeric($uID) || $uID == 0) {
            return null;
        }

        $rep = $this->entityManager->getRepository('Concrete\Core\User\User');

        $u = $rep->find($uID);
        if (!is_object($u) || $u->isError()) {
            throw new TransformationFailedException('Invalid user ID.');
        }

        return $u;
    }
}
