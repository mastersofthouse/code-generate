<?php

namespace SoftHouse\CodeGenerate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SoftHouse\ManagerTenancy\Skeleton\SkeletonClass
 */
class CodeGenerateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'code-generate';
    }
}
