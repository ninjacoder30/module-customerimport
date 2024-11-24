<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Test\Unit\Model\ImportCustomerProfile;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Phrase;
use NinjaCoder30\CustomerImport\Model\ImportCustomerProfile\CsvCustomerProfile;
use PHPUnit\Framework\TestCase;

class CsvCustomerProfileTest extends TestCase
{
    /**
     * @var Csv
     */
    private Csv $csvFileReader;

    /**
     * @var CsvCustomerProfile
     */
    private CsvCustomerProfile $csvProfile;

    /**
     * @retrun void
     */
    public function testLoadDataSuccess(): void
    {
        $sourceFile = 'var/import/sample.csv';
        $data = [
            ['firstname', 'lastname', 'email'],
            ['John', 'Doe', 'john.doe@example.com'],
            ['Jane', 'Doe', 'jane.doe@example.com'],
        ];

        $this->csvFileReader->expects($this->once())
            ->method('getData')
            ->with($sourceFile)
            ->willReturn($data);

        $expectedResult = [
            ['firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john.doe@example.com'],
            ['firstname' => 'Jane', 'lastname' => 'Doe', 'email' => 'jane.doe@example.com'],
        ];

        $result = $this->csvProfile->populateData($sourceFile);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @retrun void
     */
    public function testLoadDataFileNotFound(): void
    {
        $sourceFile = 'var/import/samplenotfound.csv';

        $this->csvFileReader->expects($this->once())
            ->method('getData')
            ->with($sourceFile)
            ->will($this->throwException(new LocalizedException(new Phrase('File not found'))));

        $this->expectException(LocalizedException::class);

        $this->csvProfile->populateData($sourceFile);
    }

    protected function setUp(): void
    {
        $this->csvFileReader = $this->createMock(Csv::class);
        $this->csvProfile = new CsvCustomerProfile($this->csvFileReader);
    }
}
