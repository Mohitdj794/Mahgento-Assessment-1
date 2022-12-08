<?php

declare(strict_types = 1);

namespace CustomCommand\ImportCustomerData\Model;

use CustomCommand\ImportCustomerData\Console\ImportCustomerDataInterface;
use Magento\Framework\Exception\LocalizedException;

class ImportJson implements ImportCustomerDataInterface
{

     /**
      * @var File
      */
      protected $driverFile;

      /**
       * @var Cutomer $CustomerFactory
       */
     protected $modelFactory;
 
     /**
      * @var Cutomer $resourceModel
      */
     protected $resourceModel;
 
     /**
      * @param File $driverFile
      * @param CustomerFactory $modelFactory
      * @param Customer $resourceModel
      */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Customer\Model\CustomerFactory $modelFactory,
        \Magento\Customer\Model\ResourceModel\Customer $resourceModel
    ) {
         $this->driverFile = $driverFile;
         $this->modelFactory = $modelFactory;
         $this->resourceModel= $resourceModel;
    }
    /**
     * @var string $path
     */
    protected string $path;

    /**
     * Export the file data into the class It supply data to import method
     */
    public function exportData()
    {

        if ($this->driverFile->isExists($this->path)) {
            $data = $this->driverFile->fileOpen($this->path, 'r');
            return json_decode($this->driverFile->fileRead($data, 2000000), true);
        }
        throw new LocalizedException(__("The File dose not Exist"));
    }

    /**
     * Import data from Customer table
     */
    public function importData() :void
    {
        $people = (array) $this->recursiveChangeKey(
            $this->exportData(),
            ['fname' => 'firstname', 'lname' => 'lastname', 'emailaddress' => 'email']
        );
        $model = $this->modelFactory->create();
        foreach ($people as $value) {
            $model->setData($value);
            $this->resourceModel->save($model);
            
        }
    }
     
    /**
     * Recursive_change_key Method
     *
     * @param array $arr
     * @param array $set
     */

    public function recursiveChangeKey($arr, $set)
    {
        if (is_array($arr) && is_array($set)) {
            $newArr = [];
            foreach ($arr as $k => $v) {
                $key = array_key_exists($k, $set) ? $set[$k] : $k;
                $newArr[$key] = is_array($v) ? $this->recursiveChangeKey($v, $set) : $v;
            }
            return $newArr;
        }
        return $arr;
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
