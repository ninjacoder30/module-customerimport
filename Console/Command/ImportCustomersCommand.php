<?php

declare(strict_types=1);

namespace NinjaCoder30\CustomerImport\Console\Command;

use Exception;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use NinjaCoder30\CustomerImport\Model\CustomerImport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCustomersCommand extends Command
{
    private const CUSTOMER_PROFILE_ARGUMENT = 'profile';

    private const SOURCE_FILE_ARGUMENT = 'source';

    /**
     * @param AppState $appState
     * @param CustomerImport $customerImport
     */
    public function __construct(
        protected AppState $appState,
        protected CustomerImport $customerImport
    ) {
        parent::__construct();
    }

    /**
     * Command to Import customers from CSV or JSON file.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('customer:import')
            ->setDescription('Import customers from CSV or JSON file.')
            ->addArgument(
                self::CUSTOMER_PROFILE_ARGUMENT,
                InputArgument::REQUIRED,
                'Import Profile like csv, json etc.'
            )
            ->addArgument(
                self::SOURCE_FILE_ARGUMENT,
                InputArgument::REQUIRED,
                'File source of the customer data'
            );

        parent::configure();
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->appState->setAreaCode('adminhtml');

        $profile = $input->getArgument(self::CUSTOMER_PROFILE_ARGUMENT);
        $fileSource = $input->getArgument(self::SOURCE_FILE_ARGUMENT);
        try {
            $importResult = $this->customerImport->importFromFile($fileSource, $profile);

            if ($importResult['success']) {
                $output->writeln('<info>' . $importResult['message'] . '</info>');
                return Cli::RETURN_SUCCESS;
            } else {
                $output->writeln('<error>' . $importResult['message'] . '</error>');
                return Cli::RETURN_FAILURE;
            }
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
    }
}
