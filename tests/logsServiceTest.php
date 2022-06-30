<?php

namespace App\Tests;

use App\Entity\Log;
use App\Service\LogsService;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class logsServiceTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $logsService;

    /**
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

    /**
     * @test
     */
    public function testToCheckFilePathIsValid()
    {
        // set up
        $filePath = 'tests/logs.txt';
        $file = $this->logsService->openLogFile($filePath);
        // testing
        $this->assertInstanceOf(SplFileObject::class, $file);
    }

    /**
     * @test
     */
    public function testToCheckFilePathIsInvalid()
    {
        $filePath = 'tests/logs2.txt';
        $file = $this->logsService->openLogFile($filePath);
        $this->assertFalse($file);
    }

    /** @test */
    public function testToCheckIfAllRecordsAreSavedFromFile()
    {
        $filePath = 'tests/logs.txt';
        $file = $this->logsService->openLogFile($filePath);
        $this->logsService->saveLogsFromFile($file);

        $logsCount = $this->entityManager
            ->getRepository(Log::class)
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
        // testing
        $this->assertEquals(1280,$logsCount);
    }

    /** @test */
    public function testToCountNumberOfRecordFroInvoiceService()
    {
        $filePath = 'tests/logs.txt';
        $file = $this->logsService->openLogFile($filePath);
        $this->logsService->saveLogsFromFile($file);

        $logsCount = $this->entityManager
            ->getRepository(Log::class)
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.serviceName = :serviceName')
            ->setParameter('serviceName', 'INVOICE-SERVICE')
            ->getQuery()
            ->getSingleScalarResult();
        // testing
        $this->assertEquals(384,$logsCount);
    }

    /** @test */
    public function testToCountNumberOfRecordForUserServiceWithDate()
    {
        $filePath = 'tests/logs.txt';
        $file = $this->logsService->openLogFile($filePath);
        $this->logsService->saveLogsFromFile($file);

        $logsCount = $this->entityManager
            ->getRepository(Log::class)
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.serviceName = :serviceName')
            ->setParameter('serviceName', 'USER-SERVICE')
            ->getQuery()
            ->getSingleScalarResult();
        // testing
        $this->assertEquals(896,$logsCount);
    }
}