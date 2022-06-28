<?php

namespace App\Tests;

use App\Entity\Log;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class logsTest extends KernelTestCase
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

    /** @test
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function logDatabaseObjectCanBeCreated()
    {
        // set up
        $log = new Log();
        $log->setServiceName('INVOICE-SERVICE');
        $logDate = new DateTime('17/Aug/2021:09:21:55 +0000');
        $log->setLogDate($logDate);
        $log->setHttpMethod('POST');
        $log->setEndPoint('/invoices');
        $log->setHttpProtocol('HTTP/1.1');
        $log->setStatusCode(201);

        $this->entityManager->persist($log);
        $this->entityManager->flush();


        $savedLog = $this->entityManager
            ->getRepository(Log::class)
            ->findOneBy(['serviceName' => 'INVOICE-SERVICE']);

        $this->assertEquals('INVOICE-SERVICE', $savedLog->getServiceName());
        $this->assertEquals(new DateTime('17/Aug/2021:09:21:55 +0000'), $savedLog->getLogDate());
        $this->assertEquals('POST', $savedLog->getHttpMethod());
        $this->assertEquals('/invoices', $savedLog->getEndPoint());
        $this->assertEquals('HTTP/1.1', $savedLog->getHttpProtocol());
        $this->assertEquals(201, $savedLog->getStatusCode());
    }
}