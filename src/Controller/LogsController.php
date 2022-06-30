<?php

namespace App\Controller;

use App\Service\LogsService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends AbstractController
{
    /**
     * @var \App\Service\LogsService
     */
    private LogsService $logsService;

    public function __construct(LogsService $logsService)
    {
        $this->logsService = $logsService;
    }

    #[Route('/count', name: 'app_logs_count', methods: 'GET')]
    public function index(Request $request): JsonResponse
    {
        try {
            // Validation of input Parameter
            $serviceNames = $request->query->get('serviceNames');
            $startDate = $request->query->get('startDate')?new DateTime($request->query->get('startDate')):null;
            $endDate = $request->query->get('endDate')?new DateTime($request->query->get('endDate')):null;
            $tempStatusCode = $request->query->get('statusCode');
            if (intval($tempStatusCode) ==  $tempStatusCode){
                $statusCode = $tempStatusCode;
            } else {
                throw new \Exception('Invalid Format for status code');
            }
            $count = $this->logsService->getLogsCount(
                $serviceNames,
                $startDate,
                $endDate,
                $statusCode
            );

//        responses: "200" count of matching results application/json:
            return $this->json([
                'count' => $count,
            ], 200);

        } catch (\Exception $exception) {
            //        responses: "400": bad input parameter
            return $this->json([
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
