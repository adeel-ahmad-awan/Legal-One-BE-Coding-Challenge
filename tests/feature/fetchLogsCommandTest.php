<?php

namespace App\Tests\feature;

use App\Entity\Log;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class fetchLogsCommandTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    /**
     * @test
     */
    public function fetchLogsCommandSavesData()
    {
        $application = new Application(self::$kernel);
        $command = $application->find('app:fetch-logs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filePath' => 'tests/feature/logs.txt'
        ]);

        $tempDateTime = new DateTime('17/Aug/2021:09:26:53 +0000');
        $savedLog = $this->entityManager
            ->getRepository(Log::class)
            ->findOneBy(['serviceName' => 'INVOICE-SERVICE', 'logDate' => $tempDateTime]);
        $this->assertEquals('INVOICE-SERVICE', $savedLog->getServiceName());
        $this->assertEquals($tempDateTime, $savedLog->getLogDate());
        $this->assertEquals('POST', $savedLog->getHttpMethod());
        $this->assertEquals('/invoices', $savedLog->getEndPoint());
        $this->assertEquals('HTTP/1.1', $savedLog->getHttpProtocol());
        $this->assertEquals(201, $savedLog->getStatusCode());

    }

    /**
     * @expectException
     */
    public function fetchLogsCommandFileNotFound()
    {
        $application = new Application(self::$kernel);
        $command = $application->find('app:fetch-logs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filePath' => 'tests/feature/logs2.txt'
        ]);
    }
}
