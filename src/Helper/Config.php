<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Helper;

use Configuration;

class Config
{
    /**
     * Retrieve API URL
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return 'https://waf.sucuri.net/api?v2';
    }

    /**
     * Retrieve key
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return Configuration::get('SUCURI_API_KEY');
    }

    /**
     * Retrieve key
     *
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return Configuration::get('SUCURI_API_SECRET');
    }

    /**
     * Retrieve option
     *
     * @param string $option
     * @return mixed[]
     */
    public function getOption(string $option): array
    {
        return $this->getOptions()[$option] ?? [];
    }

    /**
     * Retrieve all available options
     *
     * @return array[]
     */
    public function getOptions(): array
    {
        return [
            'domain' => [
                'description' => 'The domain of the site',
                'edit'        => false,
            ],
            'internal_ip_main' => [
                'description' => 'The internal IP address of the site',
                'edit'        => false,
            ],
            'proxy_active' => [
                'description' => 'One if the service is active], zero otherwise',
                'edit'        => false,
            ],
            'whitelist_list' => [
                'description' => 'A list with all the IP addresses whitelisted so far',
                'edit'        => true,
                'update'      => 'whitelist_list',
            ],
            'blacklist_list' => [
                'description' => 'A list with all the IP addresses blacklisted so far',
                'edit'        => true,
                'update'      => 'blacklist_list',
            ],
            'security_level' => [
                'description' => 'The security level chosen for your site',
                'edit'        => true,
                'update'      => 'securitylevel',
                'options'     => [
                    'high'     => 'high',
                    'paranoid' => 'paranoid',
                ],
            ],
            'cache_mode' => [
                'description' => 'The caching level chosen for your site',
                'edit'        => true,
                'update'      => 'docache',
                'options'     => [
                    'docache'      => 'docache',
                    'nocache'      => 'nocache',
                    'sitecache'    => 'sitecache',
                    'nocacheatall' => 'nocacheatall',
                ],
            ],
            'admin_access' => [
                'description' => 'Admin panel access for your site (open], restricted)',
                'edit'        => true,
                'update'      => 'adminaccess',
                'options'     => [
                    'open'       => 'open',
                    'restricted' => 'restricted',
                ],
            ],
            'comment_access' => [
                'description' => 'Comments access for your site (open], restricted)',
                'edit'        => true,
                'update'      => 'commentaccess',
                'options'     => [
                    'open'       => 'open',
                    'restricted' => 'restricted',
                ],
            ],
            'internal_domain_ip' => [
                'description' => 'Firewall IP',
                'edit'        => false,
            ],
            'internal_domain_debug_list' => [
                'description' => 'Debug URL',
                'edit'        => false,
            ],
            'compression_mode' => [
                'description' => 'Compression',
                'edit'        => true,
                'update'      => 'compression_mode',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'brotli' => [
                'description' => 'Brotli',
                'edit'        => true,
                'update'      => 'brotli',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'force_https' => [
                'description' => 'Protocol Redirection',
                'edit'        => true,
                'update'      => 'force_https',
                'options'     => [
                    'null'  => 'null',
                    'http'  => 'http',
                    'https' => 'https',
                ],
            ],
            'spdy_mode' => [
                'description' => 'HTTP/2 Support',
                'edit'        => true,
                'update'      => 'spdy_mode',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'max_upload_size' => [
                'description' => 'Maximum Upload Size',
                'edit'        => true,
                'update'      => 'max_upload_size',
                'options'     => [
                    '5m'   => '5m',
                    '10m'  => '10m',
                    '50m'  => '50m',
                    '100m' => '100m',
                    '200m' => '200m',
                    '400m' => '400m',
                ],
            ],
            'force_sec_headers' => [
                'description' => 'Add Additional Security Headers',
                'edit'        => true,
                'update'      => 'force_sec_headers',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'unfiltered_html' => [
                'description' => 'Stop unfiltered HTML from being sent to your site',
                'edit'        => true,
                'update'      => 'unfiltered_html',
                'options'     => [
                    'allow_unfilter' => 'allow_unfilter',
                    'block_unfilter' => 'block_unfilter',
                ],
            ],
            'block_php_upload' => [
                'description' => 'Stop upload of PHP or executable content',
                'edit'        => true,
                'update'      => 'block_php_upload',
                'options'     => [
                    'allow_uploads' => 'allow_uploads',
                    'block_uploads' => 'block_uploads',
                ],
            ],
            'behind_cdn' => [
                'description' => 'Site is behind CDN',
                'edit'        => true,
                'update'      => 'behind_cdn',
                'options'     => [
                    'none'              => 'none',
                    'behind_akamai'     => 'behind_akamai',
                    'behind_cloudflare' => 'behind_cloudflare',
                    'behind_maxcdn'     => 'behind_maxcdn',
                    'behind_cdn'        => 'behind_cdn',
                ],
            ],
            'http_flood_protection' => [
                'description' => 'Flood protection',
                'edit'        => true,
                'update'      => 'http_flood_protection',
                'options'     => [
                    'js_filter' => 'js_filter',
                    'disabled'  => 'disabled',
                ],
            ],
            'detect_adv_evasion' => [
                'description' => 'Advanced evasion detection',
                'edit'        => true,
                'update'      => 'detect_adv_evasion',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'aggressive_bot_filter' => [
                'description' => 'Aggressive bot filter',
                'edit'        => true,
                'update'      => 'aggressive_bot_filter',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ],
            'block_attacker_country' => [
                'description' => 'Denies access to the top attacker countries via GeoIP',
                'edit'        => true,
                'update'      => 'block_attacker_country',
                'options'     => [
                    'enabled'  => 'enabled',
                    'disabled' => 'disabled',
                ],
            ]
        ];
    }
}
