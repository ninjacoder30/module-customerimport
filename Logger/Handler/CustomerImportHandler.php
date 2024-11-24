<?php

namespace NinjaCoder30\CustomerImport\Logger\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CustomerImportHandler extends StreamHandler
{

    /**
     * Customer Import Logger
     */
    public function __construct()
    {
        $logFile = BP . '/var/log/customer_import.log';
        parent::__construct($logFile, Logger::DEBUG);
    }
}
