<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 18:18
 */
namespace Tracker\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Class Project
 * @package Tracker\UserBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_MANAGER = "ROLE_MANAGER";
    const ROLE_OPERATOR = "ROLE_OPERATOR";

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

}
