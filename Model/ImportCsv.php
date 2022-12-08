<?php

declare(strict_types = 1);

namespace CustomCommand\ImportCustomerData\Model;

use CustomCommand\ImportCustomerData\Console\ImportCustomerDataInterface;
use Magento\Framework\Exception\LocalizedException;

class ImportCsv implements ImportCustomerDataInterface
{
    /**
     * @var string $path
     */
    protected string $path;
    /**
     * @var CustomerFactory
     */
    protected $modelFactory;

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var Customer
     */
    protected $resourceModel;

    /**
     * @var array
     */
    protected array $customerData = [];

    /**
     * @param File $driverFile
     * @param Csv $csv
     * @param CustomerFactory $modelFactory
     * @param Customer $resourceModel

     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\File\Csv $csv,
        \Magento\Customer\Model\CustomerFactory $modelFactory,
        \Magento\Customer\Model\ResourceModel\Customer $resourceModel
    ) {
        $this->driverFile = $driverFile;
        $this->csv = $csv;
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Export the file data into the class It supply data to import method
     */
    public function exportData()
    {
        if ($this->driverFile->isExists($this->path)) {
              return ((array)$this->csv->getData($this->path));
        }
        throw new LocalizedException(__("The File dose not Exist"));
    }

    /**
     * Import data from Customer table
     */
    public function importData() :void
    {
        $people = (array)$this->ChangeKey(
            array_slice($this->exportData(), 1),
            [0 => 'firstname',1 => 'lastname',2 => 'email']
        );
        $model = $this->modelFactory->create();
        foreach ($people as $value) {
            $model->setData($value);
            $this->resourceModel->save($model);
        }
    }
    /**
     * Changekey Method
     *
     * @param array $arr
     * @param array $set
     */
    public function changeKey($arr, $set)
    {
        if (is_array($arr) && is_array($set)) {
            $newArr = $tempArr = [];
            foreach ($arr as $key => $value) {
                foreach ($value as $k => $v) {
                    $tempArr[$set[$k]] = $v;
                }
                $newArr[$key] = $tempArr;
            }
            return $newArr;
        }
    }
    /**
     * Execute the method
     *
     * @param string $path
     */

    public function execute(string $path)
    {
        $this->path = $path;
        $this->importData();
    }
}
