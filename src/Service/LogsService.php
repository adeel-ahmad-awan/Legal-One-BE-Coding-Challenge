<?php

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use SplFileObject;

/**
 * @class LogsService
 */
class LogsService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Psr\Log\LoggerInterface             $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getNumberOfLogsInDatabase()
    {
        return $this->entityManager
            ->getRepository(Log::class)
            ->countNumberOfLogsInDatabase();
    }

    /**
     * @param $filePath
     * @param int $logsCount
     *
     * @return false|\SplFileObject
     */
    public function openLogFile($filePath, int $logsCount = 0): bool|SplFileObject
    {
        try {
            $file = new SplFileObject($filePath, 'r');
            $file->seek($logsCount);
            return $file;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function saveLogsFromFile($file): bool
    {
        return $this->entityManager
            ->getRepository(Log::class)
            ->createLogsFromFileData($file);
    }

    /**
     * @param null $serviceNames
     * @param null $startDate
     * @param null $endDate
     * @param null $statusCode
     *
     * @return float|int|mixed|string
     */
    public function getLogsCount(
        $serviceNames = null,
        $startDate = null,
        $endDate = null,
        $statusCode = null
    ): mixed {
        return $this->entityManager
            ->getRepository(Log::class)
            ->countNumberOfLogsInDatabaseFromArguments(
                $serviceNames,
                $startDate,
                $endDate,
                $statusCode
            );
    }

}