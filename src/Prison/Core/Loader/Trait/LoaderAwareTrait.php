<?php

declare(strict_types=1);

namespace Prison\Core\Loader\Trait;

use Prison\Core\Loader\Loader;

trait LoaderAwareTrait
{
    private Loader $loader;

    public function getLoader(): Loader
    {
        return $this->loader;
    }

    public function setLoader(Loader $loader): void
    {
        $this->loader = $loader;
    }
}