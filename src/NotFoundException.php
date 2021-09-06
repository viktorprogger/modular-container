<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
