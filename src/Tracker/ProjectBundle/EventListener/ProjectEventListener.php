<?php

namespace Tracker\ProjectBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Tracker\ProjectBundle\Entity\Project;

class ProjectEventListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $project = $eventArgs->getEntity();
        $entityManager = $eventArgs->getEntityManager();

        if ($project instanceof Project) {
            if (!$project->getCode()) {
                $project->setCode($this->generateCode($project));

                $entityManager->flush();
            }
        }
    }

    /**
     * @param Project $project
     *
     * @return string
     */
    protected function generateCode(Project $project) {
        $projectLabel = $project->getLabel();

        $projectLabel = explode(' ', $projectLabel);

        $code = [];
        foreach ($projectLabel as $items) {
            $code[] = strtoupper($items[0]);
        }

        return implode('', $code).'-'.$project->getId();
    }
}
