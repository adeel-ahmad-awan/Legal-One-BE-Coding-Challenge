<?php

namespace App\Command;

use App\Entity\Log;
use DateTime;
use Doctrine\Migrations\Configuration\Exception\FileNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fetch-logs',
    description: "Fetches logs from log file, who's location is given in command file",
)]
class FetchLogsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'File path to logs file')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');
        try {
            $file = new SplFileObject($filePath);
            while(!$file->eof())
            {
                for ($i = 0; $i < 10; $i++) {
                    $singleLog = $file->fgets();
                    $arr = explode(" ", $singleLog);
                    $tempLog = new Log();
                    $tempLog->setServiceName($arr[0]);
                    $tempDate = new DateTime(trim($arr[3] . ' ' . $arr[4], '[]'));
                    $tempLog->setLogDate($tempDate);
                    $tempLog->setHttpMethod(trim($arr[5], '"'));
                    $tempLog->setEndPoint($arr[6]);
                    $tempLog->setHttpProtocol(trim($arr[7], '"'));
                    $tempLog->setStatusCode(trim($arr[8], '\n'));
                    $this->entityManager->persist($tempLog);
                }
                $this->entityManager->flush();
            }

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
        return Command::SUCCESS;
    }
}
