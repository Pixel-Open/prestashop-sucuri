<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Controller\Admin;

use Cache;
use Exception;
use Pixel\Module\Sucuri\Helper\Config;
use Pixel\Module\Sucuri\Helper\Cache as SucuriCache;
use Pixel\Module\Sucuri\Model\Api;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShopLogger;
use PrestaShopLoggerCore;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

class SucuriController extends FrameworkBundleAdminController
{
    protected const DEFAULT_LIMIT = 50;

    /**
     * @var SucuriCache $cache
     */
    private $cache;

    /**
     * @var Api $api
     */
    private $api;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @param Api $api
     * @param SucuriCache $cache
     * @param Config $config
     */
    public function __construct(Api $api, SucuriCache $cache, Config $config)
    {
        $this->api = $api;
        $this->cache = $cache;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function flushAction(Request $request): RedirectResponse
    {
        try {
            $result = $this->api->execute('clear_cache');
            foreach (($result['messages'] ?? []) as $message) {
                $this->addFlash(($result['status'] ?? 0) === 1 ? 'success' : 'error', $message);
                PrestaShopLogger::addLog(
                    $message,
                    ($result['status'] ?? 0) === 1 ?
                        PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_INFORMATIVE :
                        PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR
                );
            }
        } catch (Throwable $throwable) {
            $message = $this->trans('Unable to clear Sucuri cache.', 'Modules.Pixelsucuri.Admin');
            $message .= ' ' . $throwable->getMessage();
            $this->addFlash('error', $message);
            PrestaShopLogger::addLog($message, PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR);
        }

        return $this->redirectToRoute('admin_sucuri_settings');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function refreshAction(Request $request): RedirectResponse
    {
        try {
            $this->cache->erase(Api::SUCURI_SETTINGS_CACHE_KEY);
        } catch (Throwable $throwable) {
            $this->addFlash(
                'error',
                $this->trans('Unable to refresh the settings', 'Modules.Pixelsucuri.Admin')
            );
        }

        return $this->redirectToRoute('admin_sucuri_settings');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function settingsAction(Request $request): Response
    {
        $this->checkConfig();

        $this->setAction('sucuri_settings');

        $limit = $request->get('limit') ?: self::DEFAULT_LIMIT;
        $offset = $request->get('offset') ?: 0;

        $searchCriteria = new SearchCriteria([], null, null, $offset, $limit);

        /** @var GridFactory $settingsGridFactory */
        $settingsGridFactory = $this->get('pixel.sucuri.grid.settings_grid_factory');
        $settingsGrid = $settingsGridFactory->getGrid($searchCriteria);

        $request->query->set('limit', $limit);

        return $this->render('@Modules/pixel_sucuri/views/templates/admin/settings.html.twig', [
            'settingsGrid' => $this->presentGrid($settingsGrid),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function editAction(Request $request): Response
    {
        $param = $request->get('param');

        if (!$param) {
            return $this->redirectToRoute('admin_sucuri_settings');
        }

        $settings = $this->api->getSettings();
        if (!isset($settings[$param])) {
            return $this->redirectToRoute('admin_sucuri_settings');
        }

        if (isset($request->get('option')['value'])) {
            try {
                $result = $this->api->update($param, (string)$request->get('option')['value']);
                foreach (($result['messages'] ?? []) as $message) {
                    $this->addFlash(($result['status'] ?? 0) === 1 ? 'success' : 'error', $message);
                    PrestaShopLogger::addLog(
                        $message,
                        ($result['status'] ?? 0) === 1 ?
                            PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_INFORMATIVE :
                            PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR
                    );
                }
            } catch (Throwable $throwable) {
                $message = $this->trans('Unable to update option.', 'Modules.Pixelsucuri.Admin');
                $message .= ' ' . $throwable->getMessage();
                $this->addFlash('error', $message);
                PrestaShopLogger::addLog($message, PrestaShopLoggerCore::LOG_SEVERITY_LEVEL_ERROR);
            }
        }

        $value = json_decode($settings[$param]['value']);

        /** @var FormBuilder $optionFormBuilder */
        $optionFormBuilder = $this->get('pixel.sucuri.form.option_form_builder');
        $optionForm = $optionFormBuilder->getForm(
            ['value' => is_array($value) ? join(',', $value) : $value],
            ['label_help_box' => is_array($value) ? 'Comma separated' : null, 'label' => $param]
        );
        $optionForm->handleRequest($request);

        return $this->render('@Modules/pixel_sucuri/views/templates/admin/edit.html.twig', [
            'optionForm' => $optionForm->createView(),
            'enableSidebar' => true
        ]);
    }

    /**
     * Check the configuration and add an error message
     *
     * @return void
     */
    protected function checkConfig(): void
    {
        $errors = [];
        if (!$this->config->getApiUrl()) {
            $errors[] = $this->trans('Sucuri API URL is missing', 'Modules.Pixelsucuri.Admin');
        }
        if (!$this->config->getKey()) {
            $errors[] = $this->trans('Sucuri API key is missing', 'Modules.Pixelsucuri.Admin');
        }
        if (!$this->config->getSecret()) {
            $errors[] = $this->trans('Sucuri API secret is missing', 'Modules.Pixelsucuri.Admin');
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
        }
    }

    /**
     * Set controller action name
     *
     * @param string $action
     * @return void
     */
    protected function setAction(string $action): void
    {
        Cache::store('controller_action', $action);
    }
}
