<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 18:18
 */
namespace Tracker\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Project
 * @package Tracker\UserBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User
{
    protected $email;
    protected $username;
    protected $fulName;
    protected $avatar;
    protected $password;
    protected $roles;
}
