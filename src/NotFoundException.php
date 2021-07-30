<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

use RuntimeException;

class NotFoundException extends RuntimeException implements \Psr\Container\NotFoundExceptionInterface
{
}