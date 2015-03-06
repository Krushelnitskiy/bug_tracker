<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 18:59
 */

namespace Tracker\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Priority
 * @package Tracker\IssueBundle\Entity
 * @ORM\Table(name="issue_priority")
 * @ORM\Entity
 */

class Priority
{
    const PRIORITY_TRIVIAL = 'Trivial';
    const PRIORITY_MINOR = 'Minor';
    const PRIORITY_MAJOR = 'Major';
    const PRIORITY_CRITICAL = 'Critical';
    const PRIORITY_BLOCKER = 'Blocker';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", unique=true)
     */
    protected $value;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Priority
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
