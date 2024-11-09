<?php

declare(strict_types=1);

namespace Prison\Core\Loader\Interface;

use Prison\Core\Loader\Loader;

interface LoaderAwareInterface
{
    public function getLoader(): Loader;

    public function setLoader(Loader $loader): void;
}