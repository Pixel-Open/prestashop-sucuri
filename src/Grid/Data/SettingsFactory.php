<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Grid\Data;

use Exception;
use Pixel\Module\Sucuri\Model\Api;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class SettingsFactory implements GridDataFactoryInterface
{
    /**
     * @var Api $api
     */
    private $api;

    /**
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Retrieve settings data
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return GridData
     * @throws Exception
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $settings = $this->api->getSettings();
        $records = count($settings);

        $collection = array_splice(
            $settings,
            $searchCriteria->getOffset(),
            $searchCriteria->getLimit()
        );

        return new GridData(new RecordCollection($collection), $records);
    }
}
