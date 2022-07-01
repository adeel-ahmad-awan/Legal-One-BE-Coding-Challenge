<?php

namespace App\Tests;

use App\Entity\Log;
use App\Service\LogsService;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @class LogsServiceTest
 */
class LogsServiceTest extends KernelTestCase
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var LogsService $logsService */
    private LogsService $logsService;

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
    public function testToCountNumberOfRecordForUserService()
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

    /** @test
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function testToCountNumberOfRecordForInvoiceServiceWithDateRange()
    {
        $startDate = new DateTime('2010-10-01');
        $endDate = new DateTime('2010-11-21');
        $dateInterval = new DateInterval('P1D');

        $period = new DatePeriod($startDate, $dateInterval, $endDate);
        $dateRange = [];
        foreach ($period as $key => $value) {
            $dateRange[] = $value;
        }
        for ($i = 0; $i < count($dateRange); $i ++) {
            $tempLog = new Log();
            $tempLog->setServiceName('INVOICE-SERVICE');
            $tempLog->setLogDate($dateRange[$i]);
            $tempLog->setHttpMethod('GET');
            $tempLog->setEndPoint('/invoices');
            $tempLog->setHttpProtocol('HTTP/1.1');
            $tempLog->setStatusCode(400);
            $this->entityManager->persist($tempLog);
        }
        $this->entityManager->flush();

        $count = $this->logsService->getLogsCount(
            'INVOICE-SERVICE',
            $startDate,
            $endDate,
            400
        );
        $this->assertEquals(50,$count);
    }

    /** @test
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function testToCountNumberOfRecordForUserServiceForStatusCode()
    {
        $startDate = new DateTime('2010-10-01');
        $endDate = new DateTime('2010-11-21');
        $dateInterval = new DateInterval('P1D');

        $period = new DatePeriod($startDate, $dateInterval, $endDate);
        $dateRange = [];
        foreach ($period as $key => $value) {
            $dateRange[] = $value;
        }
        for ($i = 0; $i < count($dateRange); $i ++) {
            $tempLog = new Log();
            $tempLog->setServiceName('USER-SERVICE');
            $tempLog->setLogDate($dateRange[$i]);
            $tempLog->setHttpMethod('GET');
            $tempLog->setEndPoint('/invoices');
            $tempLog->setHttpProtocol('HTTP/1.1');
            $tempLog->setStatusCode(201);
            $this->entityManager->persist($tempLog);
        }
        $this->entityManager->flush();

        $count = $this->logsService->getLogsCount(
            'USER-SERVICE',
            null,
            null,
            201
        );
        $this->assertEquals(51,$count);
    }
}