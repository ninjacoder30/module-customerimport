<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Model\ImportCustomerProfile;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Phrase;
use NinjaCoder30\CustomerImport\Api\ImportCustomerProfileInterface;

class CsvCustomerProfile implements ImportCustomerProfileInterface
{
    /**
     * @param Csv $csvFileReader
     */
    public function __construct(
        protected Csv $csvFileReader
    ) {
    }

    /**
     * Read and Process the CSV file into data format
     *
     * @param string $source
     * @return array
     * @throws LocalizedException
     */
    public function populateData(string $source): array
    {
        try {
            $data = $this->csvFileReader->getData($source);
            $header = array_shift($data);
            return array_map(fn($row) => array_combine($header, $row), $data);
        } catch (Exception $e) {
            throw new LocalizedException(new Phrase('Cannot read CSV file: %1', [$e->getMessage()]));
        }
    }
}
