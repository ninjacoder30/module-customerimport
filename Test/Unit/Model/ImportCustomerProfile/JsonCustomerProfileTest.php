<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Test\Unit\Model\ImportCustomerProfile;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use NinjaCoder30\CustomerImport\Helper\FileReader;
use NinjaCoder30\CustomerImport\Model\ImportCustomerProfile\JsonCustomerProfile;
use PHPUnit\Framework\TestCase;

class JsonCustomerProfileTest extends TestCase
{
    /**
     * @var FileReader
     */
    private FileReader $fileReaderMock;

    /**
     * @var JsonCustomerProfile
     */
    private JsonCustomerProfile $jsonProfile;

    /**
     * @retrun void
     */
    public function testLoadDataSuccess(): void
    {
        $sourceFile = 'var/import/sample.json';
        $jsonContent = json_encode([
            [
                'fname' => 'John',
                'lname' => 'Doe',
                'emailaddress' => 'john.doe@example.com',
            ],
            [
                'fname' => 'Jane',
                'lname' => 'Doe',
                'emailaddress' => 'jane.doe@example.com',
            ],
        ]);

        $this->fileReaderMock->method('read')
            ->with($sourceFile)
            ->willReturn($jsonContent);

        $expectedResult = [
            [
                'fname' => 'John',
                'lname' => 'Doe',
                'emailaddress' => 'john.doe@example.com',
            ],
            [
                'fname' => 'Jane',
                'lname' => 'Doe',
                'emailaddress' => 'jane.doe@example.com',
            ],
        ];

        $result = $this->jsonProfile->populateData($sourceFile);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @retrun void
     */
    public function testLoadDataFileNotFound(): void
    {
        $sourceFile = 'var/import/nonexistent.json';

        $this->fileReaderMock->method('read')
            ->with($sourceFile)
            ->willThrowException(new FileSystemException(new Phrase('File not found: %1', [$sourceFile])));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('File not found: ' . $sourceFile);

        $this->jsonProfile->populateData($sourceFile);
    }

    /**
     * @retrun void
     */
    public function testLoadDataInvalidJson(): void
    {
        $sourceFile = 'var/import/invalid.json';
        $invalidJsonContent = '{invalid json}';

        $this->fileReaderMock->method('read')
            ->with($sourceFile)
            ->willReturn($invalidJsonContent);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid JSON data in file: ' . $sourceFile);

        $this->jsonProfile->populateData($sourceFile);
    }

    /**
     * @retrun void
     */
    protected function setUp(): void
    {
        $this->fileReaderMock = $this->createMock(FileReader::class);
        $this->jsonProfile = new JsonCustomerProfile($this->fileReaderMock);
    }
}
