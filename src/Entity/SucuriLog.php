<?php
/**
 * Copyright (C) 2025 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class SucuriLog
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="request_date", type="string", length=255, nullable=true)
     */
    private $requestDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_time", type="string", length=255, nullable=true)
     */
    private $requestTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remote_addr", type="string", length=255, nullable=true)
     */
    private $remoteAddr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_method", type="string", length=255, nullable=true)
     */
    private $requestMethod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="resource_path", type="text", length=65535, nullable=true)
     */
    private $resourcePath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="http_protocol", type="string", length=255, nullable=true)
     */
    private $httpProtocol;

    /**
     * @var int|null
     *
     * @ORM\Column(name="http_status", type="integer", nullable=true)
     */
    private $httpStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="http_user_agent", type="string", length=255, nullable=true)
     */
    private $httpUserAgent;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sucuri_is_allowed", type="integer", nullable=true)
     */
    private $sucuriIsAllowed;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sucuri_is_blocked", type="integer", nullable=true)
     */
    private $sucuriIsBlocked;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sucuri_block_reason", type="string", length=255, nullable=true)
     */
    private $sucuriBlockReason;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_country_name", type="string", length=255, nullable=true)
     */
    private $requestCountryName;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=255, nullable=false, unique=true)
     */
    private $checksum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="full_date", type="string", length=255, nullable=true)
     */
    private $fullDate;

    /**
     * @var int
     *
     * @ORM\Column(name="shop_id", type="integer", nullable=false)
     */
    private $shopId;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set requestDate.
     *
     * @param string|null $requestDate
     *
     * @return SucuriLog
     */
    public function setRequestDate($requestDate = null)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get requestDate.
     *
     * @return string|null
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set requestTime.
     *
     * @param string|null $requestTime
     *
     * @return SucuriLog
     */
    public function setRequestTime($requestTime = null)
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    /**
     * Get requestTime.
     *
     * @return string|null
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * Set remoteAddr.
     *
     * @param string|null $remoteAddr
     *
     * @return SucuriLog
     */
    public function setRemoteAddr($remoteAddr = null)
    {
        $this->remoteAddr = $remoteAddr;

        return $this;
    }

    /**
     * Get remoteAddr.
     *
     * @return string|null
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * Set requestMethod.
     *
     * @param string|null $requestMethod
     *
     * @return SucuriLog
     */
    public function setRequestMethod($requestMethod = null)
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    /**
     * Get requestMethod.
     *
     * @return string|null
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Set resourcePath.
     *
     * @param string|null $resourcePath
     *
     * @return SucuriLog
     */
    public function setResourcePath($resourcePath = null)
    {
        $this->resourcePath = $resourcePath;

        return $this;
    }

    /**
     * Get resourcePath.
     *
     * @return string|null
     */
    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    /**
     * Set httpProtocol.
     *
     * @param string|null $httpProtocol
     *
     * @return SucuriLog
     */
    public function setHttpProtocol($httpProtocol = null)
    {
        $this->httpProtocol = $httpProtocol;

        return $this;
    }

    /**
     * Get httpProtocol.
     *
     * @return string|null
     */
    public function getHttpProtocol()
    {
        return $this->httpProtocol;
    }

    /**
     * Set httpStatus.
     *
     * @param int|null $httpStatus
     *
     * @return SucuriLog
     */
    public function setHttpStatus($httpStatus = null)
    {
        $this->httpStatus = $httpStatus;

        return $this;
    }

    /**
     * Get httpStatus.
     *
     * @return int|null
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Set httpUserAgent.
     *
     * @param string|null $httpUserAgent
     *
     * @return SucuriLog
     */
    public function setHttpUserAgent($httpUserAgent = null)
    {
        $this->httpUserAgent = $httpUserAgent;

        return $this;
    }

    /**
     * Get httpUserAgent.
     *
     * @return string|null
     */
    public function getHttpUserAgent()
    {
        return $this->httpUserAgent;
    }

    /**
     * Set sucuriIsAllowed.
     *
     * @param int|null $sucuriIsAllowed
     *
     * @return SucuriLog
     */
    public function setSucuriIsAllowed($sucuriIsAllowed = null)
    {
        $this->sucuriIsAllowed = $sucuriIsAllowed;

        return $this;
    }

    /**
     * Get sucuriIsAllowed.
     *
     * @return int|null
     */
    public function getSucuriIsAllowed()
    {
        return $this->sucuriIsAllowed;
    }

    /**
     * Set sucuriIsBlocked.
     *
     * @param int|null $sucuriIsBlocked
     *
     * @return SucuriLog
     */
    public function setSucuriIsBlocked($sucuriIsBlocked = null)
    {
        $this->sucuriIsBlocked = $sucuriIsBlocked;

        return $this;
    }

    /**
     * Get sucuriIsBlocked.
     *
     * @return int|null
     */
    public function getSucuriIsBlocked()
    {
        return $this->sucuriIsBlocked;
    }

    /**
     * Set sucuriBlockReason.
     *
     * @param string|null $sucuriBlockReason
     *
     * @return SucuriLog
     */
    public function setSucuriBlockReason($sucuriBlockReason = null)
    {
        $this->sucuriBlockReason = $sucuriBlockReason;

        return $this;
    }

    /**
     * Get sucuriBlockReason.
     *
     * @return string|null
     */
    public function getSucuriBlockReason()
    {
        return $this->sucuriBlockReason;
    }

    /**
     * Set requestCountryName.
     *
     * @param string|null $requestCountryName
     *
     * @return SucuriLog
     */
    public function setRequestCountryName($requestCountryName = null)
    {
        $this->requestCountryName = $requestCountryName;

        return $this;
    }

    /**
     * Get requestCountryName.
     *
     * @return string|null
     */
    public function getRequestCountryName()
    {
        return $this->requestCountryName;
    }

    /**
     * Set checksum.
     *
     * @param string $checksum
     *
     * @return SucuriLog
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum.
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set fullDate.
     *
     * @param string|null $fullDate
     *
     * @return SucuriLog
     */
    public function setFullDate($fullDate = null)
    {
        $this->fullDate = $fullDate;

        return $this;
    }

    /**
     * Get fullDate.
     *
     * @return string|null
     */
    public function getFullDate()
    {
        return $this->fullDate;
    }

    /**
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return SucuriLog
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get shopId.
     *
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
