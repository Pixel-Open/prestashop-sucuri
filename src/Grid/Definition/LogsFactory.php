<?php
/**
 * Copyright (C) 2025 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class LogsFactory extends AbstractGridDefinitionFactory
{
    /**
     * Retrieve grid identifier
     *
     * @return string
     */
    protected function getId(): string
    {
        return 'sucuri_logs';
    }

    /**
     * Retrieve grid name
     *
     * @return string
     */
    protected function getName(): string
    {
        return $this->trans('Logs', [], 'Modules.Pixelsucuri.Admin');
    }

    /**
     * Retrieve columns definition
     *
     * @return ColumnCollection
     */
    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id'))
                ->setName($this->trans('ID', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'id',
                ])
            )
            ->add((new DateTimeColumn('full_date'))
                ->setName($this->trans('Date', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'full_date',
                ])
            )
            ->add((new DataColumn('remote_addr'))
                ->setName($this->trans('IP', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'remote_addr',
                ])
            )
            ->add((new DataColumn('request_method'))
                ->setName($this->trans('Method', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'request_method',
                ])
            )
            ->add((new DataColumn('resource_path'))
                ->setName($this->trans('Path', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'resource_path',
                ])
            )
            ->add((new DataColumn('http_status'))
                ->setName($this->trans('Status', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'http_status',
                ])
            )
            ->add((new DataColumn('request_country_name'))
                ->setName($this->trans('Country', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'request_country_name',
                ])
            )
            ->add((new DataColumn('sucuri_block_reason'))
                ->setName($this->trans('Reason', [], 'Modules.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'sucuri_block_reason',
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add((new Filter('remote_addr', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('remote_addr')
            )
            ->add((new Filter('full_date', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('full_date')
            )
            ->add((new Filter('resource_path', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('resource_path')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('sucuri_block_reason')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => 'sucuri_logs',
                    ],
                    'redirect_route' => 'admin_sucuri_logs',
                ])
            );

    }

        /**
     * {@inheritdoc}
     */
    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }
}
