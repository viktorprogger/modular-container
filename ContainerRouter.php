<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

class ContainerRouter
{
    private array $moduleContainers;

    public function __construct(ContainerInterface ...$moduleContainers)
    {
        $this->moduleContainers = $moduleContainers;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        foreach ($this->moduleContainers as $container) {
            if ($container->serve($id)) {
                return $container->get($id);
            }
        }

        throw new NofFoundException();
    }
}
