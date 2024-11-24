<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Test\Unit\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use NinjaCoder30\CustomerImport\Api\ImportCustomerProfileInterface;
use NinjaCoder30\CustomerImport\Model\CustomerImport;
use PHPUnit\Framework\TestCase;

class CustomerImportTest extends TestCase
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var CustomerInterfaceFactory
     */
    private CustomerInterfaceFactory $customerFactory;

    /**
     * @var ImportCustomerProfileInterface
     */
    private ImportCustomerProfileInterface $csvProfile;

    /**
     * @var CustomerImport
     */
    private CustomerImport $customerImport;

    public function testImportFromFileSuccess(): void
    {
        $sourceFile = 'var/import/sample.csv';
        $data = [['fname' => 'John', 'lname' => 'Doe', 'emailaddress' => 'john.doe@example.com']];

        $this->csvProfile->method('populateData')->with($sourceFile)->willReturn($data);
        $customer = $this->createMock(CustomerInterface::class);
        $this->customerFactory->method('create')->willReturn($customer);
        $this->customerRepository->expects($this->once())->method('save')->with($customer);

        $this->customerImport->importFromFile($sourceFile, 'csv');
    }

    public function testImportFromFileProfileNotFound(): void
    {
        $source = 'var/import/sample.csv';
        $profile = 'nonexistent';

        $customerImport = new CustomerImport(
            $this->customerRepository,
            $this->customerFactory,
            []
        );

        $this->expectException(LocalizedException::class);
        $customerImport->importFromFile($source, $profile);
    }

    protected function setUp(): void
    {
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerFactory = $this->createMock(CustomerInterfaceFactory::class);
        $this->csvProfile = $this->createMock(ImportCustomerProfileInterface::class);

        $this->customerImport = new CustomerImport(
            $this->customerRepository,
            $this->customerFactory,
            ['csv' => $this->csvProfile]
        );
    }
}
