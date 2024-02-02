<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeProviderInterface;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Pixel_sucuri extends Module
{
    /**
     * Module's constructor.
     */
    public function __construct()
    {
        $this->name = 'pixel_sucuri';
        $this->version = '1.1.0';
        $this->author = 'Pixel Open';
        $this->tab = 'administration';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans(
            'Sucuri',
            [],
            'Modules.Pixelsucuri.Admin'
        );
        $this->description = $this->trans(
            'Manage your Sucuri WAF in Prestashop.',
            [],
            'Modules.Pixelsucuri.Admin'
        );
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        $tabNames = [];
        foreach (Language::getLanguages() as $lang) {
            $tabNames[$lang['locale']] = 'Sucuri';
        }
        $this->tabs = [
            [
                'route_name' => 'admin_sucuri_settings',
                'class_name' => 'AdminPixelSucuri',
                'visible' => true,
                'name' => $tabNames,
                'parent_class_name' => 'AdminAdvancedParameters',
                'wording' => 'Sucuri',
                'wording_domain' => 'Modules.Pixelsucuri.Admin',
            ],
        ];
    }

    /***************************/
    /** MODULE INITIALIZATION **/
    /***************************/

    /**
     * Install the module
     *
     * @return bool
     */
    public function install(): bool
    {
        return parent::install() &&
            $this->registerHook('displayDashboardToolbarTopMenu') &&
            $this->registerHook('actionClearCompileCache');
    }

    /**
     * Uninstall the module
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return parent::uninstall() && $this->deleteConfigurations();
    }

    /**
     * Use the new translation system
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /***********/
    /** HOOKS **/
    /***********/

    /**
     * Clear Sucuri cache
     *
     * @param mixed[] $params
     *
     * @return void
     * @throws Exception
     */
    public function hookActionClearCompileCache(array $params): void
    {
        try {
            $result = $this->get('pixel.sucuri.api')->execute('clear_cache');
            foreach (($result['messages'] ?? []) as $message) {
                PrestaShopLogger::addLog(
                    $message,
                    ($result['status'] ?? 0) === 1 ?
                        PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_INFORMATIVE :
                        PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR
                );
            }
        } catch (Throwable $throwable) {
            PrestaShopLogger::addLog(
                $this->trans('Unable to clear Sucuri cache', [],'Modules.Pixelsucuri.Admin'),
                PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR
            );
        }
    }

    /**
     * Add toolbar buttons
     *
     * @param mixed[] $params
     *
     * @return string
     * @throws Exception
     */
    public function hookDisplayDashboardToolbarTopMenu(array $params): string
    {
        if (Cache::retrieve('controller_action') !== 'sucuri_settings') {
            return '';
        }

        $buttons = [
            [
                'label' => $this->trans('Flush Sucuri Cache', [], 'Modules.Pixelsucuri.Admin'),
                'route' => 'admin_sucuri_flush',
                'class' => 'btn btn-info',
            ]
        ];

        return $this->get('twig')->render('@Modules/pixel_sucuri/views/templates/admin/toolbar.html.twig', [
            'buttons' => $buttons,
        ]);
    }

    /*******************/
    /** CONFIGURATION **/
    /*******************/

    /**
     * Retrieve config fields
     *
     * @return array[]
     */
    protected function getConfigFields(): array
    {
        return [
            'SUCURI_API_KEY' => [
                'type'     => 'text',
                'label'    => $this->trans('Sucuri API Key', [], 'Modules.Pixelsucuri.Admin'),
                'name'     => 'SUCURI_API_KEY',
                'size'     => 20,
                'required' => true,
            ],
            'SUCURI_API_SECRET' => [
                'type'     => 'text',
                'label'    => $this->trans('Sucuri API Secret', [], 'Modules.Pixelsucuri.Admin'),
                'name'     => 'SUCURI_API_SECRET',
                'size'     => 20,
                'required' => true,
            ]
        ];
    }

    /**
     * This method handles the module's configuration page
     *
     * @return string
     */
    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            foreach ($this->getConfigFields() as $field) {
                $value = (string) Tools::getValue($field['name']);
                if ($field['required'] && empty($value)) {
                    return $this->displayError(
                        $this->trans('%field% is empty', ['%field%' => $field['label']], 'Modules.Pixelsucuri.Admin')
                    ) . $this->displayForm();
                }
                Configuration::updateValue($field['name'], $value);
            }

            $output = $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Pixelsucuri.Admin'));
        }

        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     *
     * @return string
     */
    public function displayForm(): string
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Pixelsucuri.Admin'),
                ],
                'input' => $this->getConfigFields(),
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Pixelsucuri.Admin'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        foreach ($this->getConfigFields() as $field) {
            $helper->fields_value[$field['name']] = Tools::getValue(
                $field['name'],
                Configuration::get($field['name'])
            );
        }

        return $helper->generateForm([$form]);
    }

    /**
     * Delete configurations
     *
     * @return bool
     */
    protected function deleteConfigurations(): bool
    {
        foreach ($this->getConfigFields() as $key => $options) {
            Configuration::deleteByName($key);
        }

        return true;
    }
}
