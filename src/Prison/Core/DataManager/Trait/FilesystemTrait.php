<?php

declare(strict_types=1);

namespace Prison\Core\DataManager\Trait;

use Prison\Core\DataManager\DataManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

trait FilesystemTrait
{
    private Filesystem $filesystem;

    public function createDirectory(): void
    {
        if ($this instanceof DataManagerInterface) {
            $this->filesystem->mkdir($this->getDirectoryPath());
        }
    }

    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }
}
