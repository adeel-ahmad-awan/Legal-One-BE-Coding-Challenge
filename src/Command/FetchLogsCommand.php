<?php

namespace App\Command;

use App\Entity\Log;
use App\Service\LogsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch-logs',
    description: "Fetches logs from log file, who's location is given in command file",
)]
class FetchLogsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private LogsService $logsService;

    const FILE_PATH = 'filePath';
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        LogsService $logsService
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->logsService = $logsService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::FILE_PATH, InputArgument::REQUIRED, 'File path to logs file')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $filePath = $input->getArgument(self::FILE_PATH);
            $logsCount = $this->logsService->getNumberOfLogsInDatabase();
            $file = $this->logsService->openLogFile($filePath, $logsCount);
            $this->logsService->saveLogsFromFile($file);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            dump('failed to execute app:fetch-logs. Please check logs for details');
            return Command::FAILURE;
        }
        dump('All logs saved successfully');
        return Command::SUCCESS;
    }
}
