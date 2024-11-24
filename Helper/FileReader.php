<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;

class FileReader
{
    /**
     * @param File $file
     */
    public function __construct(protected File $file)
    {
    }

    /**
     * Check if file exists
     *
     * @param string $filePath
     * @return string
     * @throws FileSystemException
     */
    public function read(string $filePath): string
    {
        if (!$this->file->isExists($filePath)) {
            throw new FileSystemException(__('File not found: %1', $filePath));
        }

        return $this->file->fileGetContents($filePath);
    }
}
