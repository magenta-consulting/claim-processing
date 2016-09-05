<?php
namespace AppBundle\Admin\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RolesTransformer implements DataTransformerInterface
{

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  ArrayCollection|null $issue
     * @return string
     */
    public function transform($data)
    {
        return $data[0];
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $userId
     * @return ArrayCollection|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($data)
    {
        return array($data);
    }
}