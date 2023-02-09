<?php


namespace TheCodingMachine\GraphQL\Controllers;

use Psr\Container\ContainerInterface;
use Doctrine\Common\Annotations\Reader;
use TheCodingMachine\GraphQL\Controllers\Security\AuthenticationServiceInterface;
use TheCodingMachine\GraphQL\Controllers\Security\AuthorizationServiceInterface;
use Youshido\GraphQL\Field\Field;

/**
 * A query provider that looks into all controllers of your application to fetch queries.
 */
class AggregateControllerQueryProvider implements QueryProviderInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;
    /**
     * @var TypeMapperInterface
     */
    private $typeMapper;
    /**
     * @var HydratorInterface
     */
    private $hydrator;
    /**
     * @var array|\string[]
     */
    private $controllers;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;
    /**
     * @var AuthorizationServiceInterface
     */
    private $authorizationService;

    /**
     * @param string[] $controllers A list of controllers name in the container.
     */
    public function __construct(array $controllers, ContainerInterface $container, Reader $annotationReader, TypeMapperInterface $typeMapper, HydratorInterface $hydrator, AuthenticationServiceInterface $authenticationService, AuthorizationServiceInterface $authorizationService)
    {
        $this->controllers = $controllers;
        $this->container = $container;
        $this->annotationReader = $annotationReader;
        $this->typeMapper = $typeMapper;
        $this->hydrator = $hydrator;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @return Field[]
     */
    public function getQueries(): array
    {
        $queryList = [];

        foreach ($this->controllers as $controllerName) {
            $controller = $this->container->get($controllerName);
            $queryProvider = new ControllerQueryProvider($controller, $this->annotationReader, $this->typeMapper, $this->hydrator, $this->authenticationService, $this->authorizationService, $this->container);
            $queryList = array_merge($queryList, $queryProvider->getQueries());
        }

        return $queryList;
    }

    /**
     * @return Field[]
     */
    public function getMutations(): array
    {
        $mutationList = [];

        foreach ($this->controllers as $controllerName) {
            $controller = $this->container->get($controllerName);
            $queryProvider = new ControllerQueryProvider($controller, $this->annotationReader, $this->typeMapper, $this->hydrator, $this->authenticationService, $this->authorizationService, $this->container);
            $mutationList = array_merge($mutationList, $queryProvider->getMutations());
        }

        return $mutationList;
    }
}
