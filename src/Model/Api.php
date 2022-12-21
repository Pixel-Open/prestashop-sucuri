<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Model;

use Exception;
use Pixel\Module\Sucuri\Helper\Cache;
use Pixel\Module\Sucuri\Helper\Config;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Api
{
    public const SUCURI_SETTINGS_CACHE_KEY = 'sucuri_settings';

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var HttpClientInterface $client
     */
    private $client;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @param HttpClientInterface $client
     * @param Config $config
     * @param Cache $cache
     */
    public function __construct(HttpClientInterface $client, Config $config, Cache $cache)
    {
        $this->client = $client;
        $this->config = $config;
        $this->cache  = $cache;
    }

    /**
     * Execute api method
     *
     * @param string  $method
     * @param mixed[] $params
     *
     * @return mixed[]
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function execute(string $method, array $params = []): array
    {
        if (!($this->config->getApiUrl() && $this->config->getKey() && $this->config->getSecret())) {
            return ['status' => 0];
        }

        $body = [
            'k' => $this->config->getKey(),
            's' => $this->config->getSecret(),
            'a' => $method
        ];

        $body = array_merge($body, $params);

        $response = $this->client->request('POST', $this->config->getApiUrl(), ['body' => $body]);

        return json_decode($response->getContent(), true);
    }

    /**
     * Retrieve setting
     *
     * @return mixed[]
     *
     * @throws Exception
     */
    public function getSettings(): array
    {
        if (!$this->cache->isCached(self::SUCURI_SETTINGS_CACHE_KEY)) {
            $settings = [];
            try {
                $result = $this->execute('show_settings');
                if (($result['status'] ?? 0) === 1) {
                    foreach ($result['output'] as $option => $value) {
                        $settings[$option] = [
                            'option' => $option,
                            'value' => json_encode($value),
                            'description' => $this->getOptionDescription($option),
                            'can_edit' => $this->canEditOption($option),
                        ];
                    }
                }
            } catch (Throwable $throwable) {}

            $this->cache->store(self::SUCURI_SETTINGS_CACHE_KEY, $settings);
        }

        return $this->cache->retrieve(self::SUCURI_SETTINGS_CACHE_KEY);
    }

    /**
     * Update parameter
     *
     * @param string $param
     * @param string $value
     *
     * @return mixed[]
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function update(string $param, string $value): array
    {
        $result = [
            'status'   => 0,
            'messages' => [],
        ];

        if (!$this->canEditOption($param)) {
            $result['messages'][] = 'Not allowed';
            return $result;
        }

        $param = $this->getOptionApiParamName($param);

        if (in_array($param, ['whitelist_list', 'blacklist_list'])) {
            $type = str_replace('_list', '', $param);
            $current = json_decode($this->getSettings()[$param]['value'], true);
            $requested = array_map('trim', explode(',', $value));

            foreach ($requested as $ip) {
                if (!$ip) {
                    continue;
                }
                if (in_array($ip, $current)) {
                    continue;
                }
                $response = $this->execute($type . '_ip', ['ip' => $ip]);
                $result['status'] = $response['status'];
                $result['messages'] = array_merge($result['messages'], $response['messages']);
            }
            foreach ($current as $ip) {
                if (!$ip) {
                    continue;
                }
                if (in_array($ip, $requested)) {
                    continue;
                }
                $response = $this->execute('delete_' . $type . '_ip', ['ip' => $ip]);
                $result['status'] = $response['status'];
                $result['messages'] = array_merge($result['messages'], $response['messages']);
            }
        } else {
            $result = $this->execute('update_setting', [$param => $value]);
        }

        $this->cache->erase(Api::SUCURI_SETTINGS_CACHE_KEY);

        return $result;
    }

    /**
     * Is option editable
     *
     * @param string $option
     * @return bool
     */
    protected function canEditOption(string $option): bool
    {
        return $this->config->getOption($option)['edit'] ?? false;
    }

    /**
     * Retrieve option description
     *
     * @param string $option
     * @return string
     */
    protected function getOptionDescription(string $option): string
    {
        return $this->config->getOption($option)['description'] ?? '';
    }

    /**
     * Retrieve option updatable param
     *
     * @param string $option
     * @return string
     */
    protected function getOptionApiParamName(string $option): string
    {
        return $this->config->getOption($option)['update'] ?? $option;
    }
}
