<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Model\ImportCustomerProfile;

use Magento\Framework\Exception\LocalizedException;
use NinjaCoder30\CustomerImport\Api\ImportCustomerProfileInterface;
use NinjaCoder30\CustomerImport\Helper\FileReader;

class JsonCustomerProfile implements ImportCustomerProfileInterface
{
    /**
     * JsonProfile Constructor
     *
     * @param FileReader $fileReader
     */
    public function __construct(private FileReader $fileReader)
    {
    }

    /**
     * Load data from the given file path.
     *
     * @param string $source
     * @return array
     */
    public function populateData(string $source): array
    {
        $jsonContent = $this->fileReader->read($source);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LocalizedException(__('Invalid JSON data in file: %1', [$source]));
        }
        return $data;
    }
}
