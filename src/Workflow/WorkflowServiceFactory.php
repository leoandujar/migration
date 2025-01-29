<?php

namespace App\Workflow;

use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Model\Repository\WorkflowRepository;
use App\Service\LoggerService;
use App\Service\UtilService;
use App\Workflow\HelperServices\ProjectWorkflowService;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\Workflow\Registry;

class WorkflowServiceFactory
{
    private array $dependencies = [];
    public function __construct(
        EntityManagerInterface $em,
        Registry $registry,
        LoggerService $loggerSrv,
        ContactPersonRepository $contactPersonRepo,
        WorkflowRepository $workflowRepository,
        WorkflowMonitorRepository $wfMonitorRepo,
        UtilService $utilsSrv,
        ProjectWorkflowService $projectWorkflowSrv,
    )
    {
        $this->dependencies = [
            "em" => $em,
            "registry" => $registry,
            "loggerSrv" => $loggerSrv,
            "contactPersonRepo" => $contactPersonRepo,
            "workflowRepository" => $workflowRepository,
            "wfMonitorRepo" => $wfMonitorRepo,
            "utilsSrv" => $utilsSrv,
            "projectWorkflowSrv" => $projectWorkflowSrv,
        ];
    }

    /**
     * Creates an instance of the given workflow class.
     *
     * @param string $workflowClass
     * @return mixed
     */
    public function getStartClass(string $workflowClass): mixed
    {
        try {
            $reflector = new ReflectionClass($workflowClass);
            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                return new $workflowClass;
            }

            $parameters = $constructor->getParameters();

            $finalDependencies = [];
            foreach ($parameters as $parameter) {
                $finalDependencies[] = $this->dependencies[$parameter->getName()];
            }

            return $reflector->newInstanceArgs($finalDependencies);

        } catch (\Throwable $th) {
            return null;
        }
    }

}