<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Grid\Definition;

use Pixel\Module\Sucuri\Grid\Action\Row\EditAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

final class SettingsFactory extends AbstractGridDefinitionFactory
{
    /**
     * Retrieve grid identifier
     *
     * @return string
     */
    protected function getId(): string
    {
        return 'sucuri_settings';
    }

    /**
     * Retrieve grid name
     *
     * @return string
     */
    protected function getName(): string
    {
        return $this->trans('Settings', [], 'Admin.Pixelsucuri.Admin');
    }

    /**
     * Retrieve columns definition
     *
     * @return ColumnCollection
     */
    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new DataColumn('option'))
                ->setName($this->trans('Option', [], 'Admin.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'option',
                    'sortable' => false,
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Description', [], 'Admin.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'description',
                    'sortable' => false,
                ])
            )
            ->add((new DataColumn('value'))
                ->setName($this->trans('Value', [], 'Admin.Pixelsucuri.Admin'))
                ->setOptions([
                    'field' => 'value',
                    'sortable' => false,
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_sucuri_edit',
                                'route_param_name' => 'param',
                                'route_param_field' => 'option',
                                'accessibility_checker' => new EditAccessibilityChecker(),
                                'clickable_row' => false,
                            ])
                        )
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
                    ->setName($this->trans('Refresh', [], 'Admin.Pixelsucuri.Admin'))
                    ->setIcon('refresh')
            );
    }
}
