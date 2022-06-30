<?php

namespace App\Repository;

use App\Entity\Log;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, Log::class);
    }

    public function add(Log $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Log $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countNumberOfLogsInDatabase()
    {
        return $this
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function createLogsFromFileData($file): bool
    {
        $count = 0;
        $batchInsertionCount = 100;
        try {
            while(!$file->eof())
            {
                $singleLog = trim($file->fgets());
                if($singleLog) { //to handle empty lines
                    $arr = explode(" ", $singleLog);
                    $tempLog = new Log();
                    $tempLog->setServiceName($arr[0]);
                    $tempLog->setLogDate(new DateTime(trim($arr[3] . ' ' . $arr[4], '[]')));
                    $tempLog->setHttpMethod(trim($arr[5], '"'));
                    $tempLog->setEndPoint($arr[6]);
                    $tempLog->setHttpProtocol(trim($arr[7], '"'));
                    $tempLog->setStatusCode(trim($arr[8], '\n'));
                    $this->getEntityManager()->persist($tempLog);
                }
                // batch insertion
                if ($count % $batchInsertionCount == 0 || $file->eof()) {
                    $this->getEntityManager()->flush();
                    $this->getEntityManager()->clear();
                }
                $count ++;
            }
            return false;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return true;
    }

    /**
     * @param $serviceNames
     * @param $startDate
     * @param $endDate
     * @param $statusCode
     *
     * @return float|int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countNumberOfLogsInDatabaseFromArguments(
        $serviceNames,
        $startDate,
        $endDate,
        $statusCode
    ) {
        $qb = $this->createQueryBuilder('a')->select('count(a.id)');

        // check for serviceNames
        if ($serviceNames) {
            $qb->andWhere('a.serviceName = :serviceName');
            $qb->setParameter('serviceName', $serviceNames);
        }

        // check for startDate
        if ($startDate) {
            $qb->andWhere('a.logDate > :startDate');
            $qb->setParameter('startDate', $startDate);
        }

        // check for endDate
        if ($endDate) {
            $qb->andWhere('a.logDate < :endDate');
            $qb->setParameter('endDate', $endDate);
        }

        // check for statusCode
        if ($statusCode) {
            $qb->andWhere('a.statusCode = :statusCode');
            $qb->setParameter('statusCode', $statusCode);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
