<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Concrete\Core\File\File;

/**
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class FileToIntegerTransformer implements DataTransformerInterface
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
     * Converts a concrete5 file object to an integer.
     *
     * @param \Concrete\Core\File\File $value The file object value
     * @return int The integer value
     * @throws TransformationFailedException If the given value is not an
     *                                       instance of Concrete\Core\File\File.
     */
    public function transform($value)
    {
        if ($value === null || $value == '') {
            return null;
        }
        if (!($value instanceof File)) {
            throw new TransformationFailedException('Expected an instance of a concrete5 file object.');
        }
        return intval($value->getFileID());
    }

    /**
     * Converts an integer to a concrete5 file object.
     *
     * @param int $fID
     * @return mixed The value
     * @throws TransformationFailedException If the given value is not a proper
     *                                       concrete5 file ID.
     */
    public function reverseTransform($fID)
    {
        if (!is_numeric($fID) || $fID == 0) {
            return null;
        }

        $rep = $this->entityManager->getRepository('Concrete\Core\File\File');

        $f = $rep->find($fID);
        if (!is_object($f) || $f->isError()) {
            throw new TransformationFailedException('Invalid file ID.');
        }

        return $f;
    }
}
