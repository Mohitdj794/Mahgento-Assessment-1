<?php

declare(strict_types=1);

namespace CustomCommand\ImportCustomerData\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CustomCommand\ImportCustomerData\Model\ImportJson;
use CustomCommand\ImportCustomerData\Model\ImportCsv;
use Magento\Framework\Exception\LocalizedException;

class CustomCommand extends Command
{

    private const PROFILE = 'profile';

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * @var ImportCsv
     */
    protected $importCsv;

    /**
     * @var ImportJson
     */
    protected $importJson;

    /**
     *
     * @param File $driverFile
     * @param LoggerInterface $logger
     * @param ImportCsv $importCsv
     * @param ImportJson $importJson
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Psr\Log\LoggerInterface $logger,
        ImportCsv $importCsv,
        ImportJson $importJson
    ) {
        $this->driverFile = $driverFile;
        $this->logger= $logger;
        $this->importCsv = $importCsv;
        $this->importJson = $importJson;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::PROFILE, // the option name
                '-s', // the <shortcut></shortcut>
                InputOption::VALUE_REQUIRED, // the option mode
                'Say the file extention' // the description
            ),
        ];
        $this->setName('customer:import');
        $this->setDescription('Set customer in our store');
        $this->setDefinition($options);
        $this->addArgument('sourcePath', InputArgument::REQUIRED, 'Give source path');

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
             $path = (string)$input->getArgument('sourcePath');
             $extension = $input->getOption('profile');
             $arr = explode('.', $path);
            $realExtension=end($arr);
            if ($extension != $realExtension) {
                    throw new LocalizedException(__("File is undefine or not exist"));
            }
            switch ($extension) {
                case 'json':
                     $this->importJson->execute($path);
                     $output->writeln('<info>Data Updated</info>');
                    break;
                case 'csv':
                     $this->importCsv->execute($path);
                     $output->writeln('<info>Data Updated</info>');
                    break;
                default:
                    throw new LocalizedException(__("undefine File type"));
                    
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
                $this->logger->critical($e->getMessage());
        }
    }
}
