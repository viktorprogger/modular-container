<?php

namespace Viktorprogger\Container\Test\Stub\App\ModuleRoot1;

use Viktorprogger\Container\Test\Stub\Vendor\VendorClass;

class VendorDependent
{
    public function __construct(private VendorClass $vendorClass)
    {
    }
}
