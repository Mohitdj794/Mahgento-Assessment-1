<?php

declare(strict_types = 1);

namespace CustomCommand\ImportCustomerData\Console;

interface ImportCustomerDataInterface
{

    /**
     * Export the file data into the class It supply data to import method
     */
    public function exportData();

    /**
     * Import data from Customer table
     */
    public function importData();

    /**
     * Execute the method
     *
     * @param string $path
     */
    public function execute(string $path);
}
