<?php

namespace Tracker\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use FOS\UserBundle\Model\User as BaseUser;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Tracker\IssueBundle\Entity\Issue;
use Tracker\ProjectBundle\Entity\Project;

/**
 * Class Project
 * @package Tracker\UserBundle\Entity
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_OPERATOR = 'ROLE_OPERATOR';

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $fullName
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fullName;

    /**
     * @var Project $project
     *
     * @ORM\ManyToMany(targetEntity="\Tracker\ProjectBundle\Entity\Project", mappedBy="members")
     **/
    protected $project;

    /**
     * @var string $path
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="4096k")
     * @Assert\Image(mimeTypesMessage="Please upload a valid image.")
     */
    protected $file;

    /**
     * @var Issue[] $assignedIssue
     *
     * @ORM\OneToMany(targetEntity="\Tracker\IssueBundle\Entity\Issue", mappedBy="assignee")
     **/
    protected $assignedIssue;

    public function __construct()
    {
        $this->project = new ArrayCollection();
        $this->assignedIssue = new ArrayCollection();
        parent::__construct();
    }

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
     * Add assignedIssue
     *
     * @param Issue $assignedIssue
     * @return array Issue
     */
    public function addAssignedIssue(Issue $assignedIssue)
    {
        $this->assignedIssue[] = $assignedIssue;

        return $this;
    }

    /**
     * Remove assignedIssue
     *
     * @param Issue $assignedIssue
     */
    public function removeAssignedIssue(Issue $assignedIssue)
    {
        $this->assignedIssue->removeElement($assignedIssue);
    }

    /**
     * Get assignedIssue
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssignedIssue()
    {
        return $this->assignedIssue;
    }

    /**
     * Add product
     *
     * @param \Tracker\ProjectBundle\Entity\Project $product
     *
     * @return User
     */
    public function addProduct(Project $product)
    {
        $this->project[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Tracker\ProjectBundle\Entity\Project $product
     */
    public function removeProduct(Project $product)
    {
        $this->project->removeElement($product);
    }

    /**
     * Get product
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return User
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (!isset($this->file)) {
            return;
        }
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->file->move($this->getUploadRootDir(), $this->path);
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : '/' . $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/profile';
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *s
     * @return $this
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->setPath($file->getClientOriginalName());
        return $this;
    }

    /**
     * Add project
     *
     * @param \Tracker\ProjectBundle\Entity\Project $project
     *
     * @return User
     */
    public function addProject(Project $project)
    {
        $this->project[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Tracker\ProjectBundle\Entity\Project $project
     */
    public function removeProject(Project $project)
    {
        $this->project->removeElement($project);
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }
}
