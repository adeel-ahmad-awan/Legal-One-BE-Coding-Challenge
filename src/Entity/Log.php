<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $serviceName;

    #[ORM\Column(type: 'datetime')]
    private $logDate;

    #[ORM\Column(type: 'string', length: 6)]
    private $httpMethod;

    #[ORM\Column(type: 'string', length: 50)]
    private $endPoint;

    #[ORM\Column(type: 'string', length: 10)]
    private $httpProtocol;

    #[ORM\Column(type: 'decimal', precision: 10, scale: '0')]
    private $statusCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getLogDate(): ?\DateTimeInterface
    {
        return $this->logDate;
    }

    public function setLogDate(\DateTimeInterface $logDate): self
    {
        $this->logDate = $logDate;

        return $this;
    }

    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(string $httpMethod): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getEndPoint(): ?string
    {
        return $this->endPoint;
    }

    public function setEndPoint(string $endPoint): self
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    public function getHttpProtocol(): ?string
    {
        return $this->httpProtocol;
    }

    public function setHttpProtocol(string $httpProtocol): self
    {
        $this->httpProtocol = $httpProtocol;

        return $this;
    }

    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }

    public function setStatusCode(string $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
