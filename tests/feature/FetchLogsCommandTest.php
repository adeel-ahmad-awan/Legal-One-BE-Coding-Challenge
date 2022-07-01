<?php

namespace App\Tests\feature;

use App\Entity\Log;
use App\Service\LogsService;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @class FetchLogsCommandTest
 */
class FetchLogsCommandTest extends KernelTestCase
{
    /**  @var EntityManager $entityManager */
    private $entityManager;

    /** @var LogsService $logsService */
    private $logsService;

    /**
     * @test
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->logsService = $container->get(LogsService::class);
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    /** @test */
    public function fetchLogsCommandSavesData()
    {
        $application = new Application(self::$kernel);
        $command = $application->find('app:fetch-logs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filePath' => 'tests/logs.txt'
        ]);

        $tempDateTime = new DateTime('17/Aug/2021:09:26:53 +0000');
        $savedLog = $this->entityManager
            ->getRepository(Log::class)
            ->findOneBy(['serviceName' => 'INVOICE-SERVICE', 'logDate' => $tempDateTime]);
        $expectedRecord = [$savedLog->getId(), 'INVOICE-SERVICE', $tempDateTime, 'POST', '/invoices', 'HTTP/1.1', 201];
        $savedLog = array_values((array)$savedLog);
        $this->assertEquals($expectedRecord, $savedLog);

        $logsCount = $this->logsService->getNumberOfLogsInDatabase();

        // testing
        $this->assertEquals(1280,$logsCount);
    }
}
