<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Model;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use NinjaCoder30\CustomerImport\Api\ImportCustomerProfileInterface;
use NinjaCoder30\CustomerImport\Logger\CustomerImportLogger;

class CustomerImport
{

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerImportLogger $logger
     * @param array $profiles
     */
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
        protected CustomerInterfaceFactory $customerFactory,
        protected CustomerImportLogger $logger,
        protected array $profiles = []
    ) {
    }

    /**
     * Logic to import the file data and pass the same for saving
     *
     * @param string $source
     * @param string $profile
     * @return array
     * @throws LocalizedException
     */
    public function importFromFile(string $source, string $profile): array
    {
        if (!isset($this->profiles[$profile])) {
            throw new LocalizedException(__('Import profile "%1" not found.', $profile));
        }

        $importCustomerProfile = $this->profiles[$profile];

        if (!$importCustomerProfile instanceof ImportCustomerProfileInterface) {
            throw new LocalizedException(__('Invalid import for profile "%1".', $profile));
        }

        $customerData = $importCustomerProfile->populateData($source);

        return $this->saveCustomerData($customerData);
    }

    /**
     * Save Customer Data in Application
     *
     * @param array $customerData
     * @return array
     * @throws LocalizedException
     */
    protected function saveCustomerData(array $customerData): array
    {
        $result = [
            'success' => true,
            'message' => 'File Has been successfully Imported.'
        ];

        if (empty($customerData)) {
            throw new LocalizedException(__('Empty records.'));
        }
        foreach ($customerData as $data) {
            try {
                $customer = $this->customerFactory->create();
                $customer->setFirstname($data['fname']);
                $customer->setLastname($data['lname']);
                $customer->setEmail($data['emailaddress']);
                $this->customerRepository->save($customer);
                $this->logger->info("Customer {$data['emailaddress']} imported successfully.");
            } catch (Exception $e) {
                $this->logger->info("Failed to import customer {$data['emailaddress']}: {$e->getMessage()}");
            }
        }

        return $result;
    }
}
