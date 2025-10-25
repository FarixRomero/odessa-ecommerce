<?php

namespace Webkul\YapePlin\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Models to be registered.
     *
     * @var array
     */
    protected $models = [
        \Webkul\YapePlin\Models\Receipt::class,
    ];
}
