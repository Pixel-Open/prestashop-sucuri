<?php
/**
 * Copyright (C) 2025 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Exception;
use Pixel\Module\Sucuri\Entity\SucuriLog as Entity;
use PixelOpen\Sucuri\Api\Data\LogInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SucuriLog
{
    protected EntityManager $entityManager;

    protected TranslatorInterface $translator;

    /**
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function getById(int $id): ?object
    {
        $criteria = ['id' => $id];

        return $this->findOne($criteria);
    }

    /**
     * @param string $checksum
     * @return object|null
     */
    public function getByChecksum(string $checksum): ?object
    {
        $criteria = ['checksum' => $checksum];

        return $this->findOne($criteria);
    }

    /**
     * @return Entity
     */
    public function initEmpty(): Entity
    {
        $entity = new Entity();

        $entity->setShopId(0);

        return $entity;
    }

    /**
     * @param int|null $idShop
     * @return array
     */
    public function getList(?int $idShop = null): array
    {
        $criteria = [];
        if ($idShop !== null) {
            $criteria['shopId'] = [0, $idShop];
        }

        return $this->findAll($criteria);
    }

    /**
     * @param array $data
     * @return Entity
     * @throws Exception|ORMInvalidArgumentException|ORMException
     */
    public function save(array $data): Entity
    {
        $this->validate($data);

        $entity = $this->getByChecksum($data['checksum']);
        if (!$entity) {
            $entity = $this->initEmpty();
        }

        $entity->setChecksum($data['checksum']);
        $entity->setRequestDate($data['request_date'] ?? null);
        $entity->setRequestTime($data['request_time'] ?? null);
        $entity->setRemoteAddr($data['remote_addr'] ?? null);
        $entity->setRequestMethod($data['request_method'] ?? null);
        $entity->setResourcePath($data['resource_path'] ?? null);
        $entity->setHttpProtocol($data['http_protocol'] ?? null);
        $entity->setHttpStatus($data['http_status'] ?? null);
        $entity->setHttpUserAgent($data['http_user_agent'] ?? null);
        $entity->setSucuriIsAllowed($data['sucuri_is_allowed'] ?? 0);
        $entity->setSucuriIsBlocked($data['sucuri_is_blocked'] ?? null);
        $entity->setSucuriBlockReason($data['sucuri_block_reason'] ?? null);
        $entity->setRequestCountryName($data['request_country_name'] ?? null);

        if ($entity->getRequestDate()) {
            $date = explode('/', (string)$entity->getRequestDate());
            if (isset($date[0], $date[1], $date[2])) {
                $timestamp = strtotime($date[0] . $date[1] . $date[2]);
                if ($timestamp) {
                    $entity->setFullDate(date('Y-m-d', $timestamp) . ' ' . $entity->getRequestTime());
                }
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param int $id
     * @return bool
     * @throws ORMException|OptimisticLockException
     */
    public function delete(int $id): bool
    {
        $entity = $this->entityManager->getRepository(Entity::class)->find($id);
        if (!$entity) {
            return false;
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param array $criteria
     * @return object|null
     */
    public function findOne(array $criteria): ?object
    {
        $repository = $this->entityManager->getRepository(Entity::class);

        return $repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return array[]
     */
    public function findAll(array $criteria, ?array $orderBy = null): array
    {
        $repository = $this->entityManager->getRepository(Entity::class);

        return $repository->findBy($criteria, $orderBy);
    }

    /**
     * @throws Exception
     */
    public function validate(array $data): bool
    {
        if (!($data['checksum'] ?? '')) {
            throw new Exception($this->translator->trans('Checksum is required', [], 'Modules.Pixelsucuri.Shop'));
        }

        return true;
    }
}
