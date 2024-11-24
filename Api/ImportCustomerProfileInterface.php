<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Api;

interface ImportCustomerProfileInterface
{
    /**
     * Analyze and Process the data
     *
     * @param string $source
     * @return array
     */
    public function populateData(string $source): array;
}
