<?php
 
namespace CustomCommand\ImportCustomerData\Test\Unit\Model;
 
class ImportJsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var View
     */
    protected $sampleClass;
 
    /**
     * @var string
     */
    protected $expectedMessage;
 
    public function setUp() :void
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->sampleClass = $objectManager->getObject(\CustomCommand\ImportCustomerData\Model\ImportJson::class);
        $this->expectedMessage = '/home/z0375@ad.ziffity.com/Downloads/sample.json';
    }
    
    // /**
    //  * @dataProvider additionProvider
    //  */
    // public function testImportData($path, $expected)
    // {
    //     $this->assertSame($expected, $this->sampleClass->importData($path));
    // }
    /**
     * @dataProvider dataProv
     */
    public function testRecursiveChangeKey($arr, $set, $expected)
    {
        $this->assertSame($expected, $this->sampleClass->recursiveChangeKey($arr, $set));
    }

    public function dataProv(): array
    {
        return [
            [
               'arr' => ['fname'=> 'adssa','lname'=>'ssasad','emailadress'=>'asdsa@gmail.com'],
               'set' => ['fname' => 'firstname', 'lname' => 'lastname', 'emailadress' => 'email'],
               'expected' => [
                            'firstname' => 'adssa',
                            'lastname' => 'ssasad',
                            'email' => 'asdsa@gmail.com',
                            
                ],
            ],
            [
                'arr' => [['fname'=> 'adssa','lname'=>'ssasad','emailadress'=>'asdsa@gmail.com'],
                          ['fname'=> 'adssa','lname'=>'ssasad','emailadress'=>'asdsa@gmail.com']],
                'set' => ['fname' => 'firstname', 'lname' => 'lastname', 'emailadress' => 'email'],
                'expected' => [
                    [
                        'firstname' => 'adssa',
                        'lastname' => 'ssasad',
                        'email' => 'asdsa@gmail.com',
                    ],
                    [
                        'firstname' => 'adssa',
                        'lastname' => 'ssasad',
                        'email' => 'asdsa@gmail.com',
                    ]
                 ],
             ]
        ];
    }
}
