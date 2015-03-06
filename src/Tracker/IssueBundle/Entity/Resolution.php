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
 * Class Resolution
 * @package Tracker\IssueBundle\Entity
 * @ORM\Table(name="issue_resolution")
 * @ORM\Entity
 */

class Resolution
{
    const RESOLUTION_FIXED = 'Fixed';
    const RESOLUTION_WONT_FIX = 'Won`t fix';
    const RESOLUTION_DONE = 'Done';
    const RESOLUTION_WONT_DO = 'Won`t done';

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
     * @return Resolution
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
