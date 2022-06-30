<?php

namespace App\DataFixtures;

use App\Service\LogsService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LogsFixture extends Fixture
{
    /**
     * @var \App\Service\LogsService
     */
    private LogsService $logsService;

    public function __construct(LogsService $logsService)
    {
        $this->logsService = $logsService;
    }

    public function load(ObjectManager $manager): void
    {
        $filePath = 'tests/logs.txt';
        $file = $this->logsService->openLogFile($filePath);
        $this->logsService->saveLogsFromFile($file);
    }
}
