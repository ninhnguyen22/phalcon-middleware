<?php

namespace Nin\Middleware;

use Phalcon\Config\Config;

/**
 * Read middleware from config
 *
 * @package Nin\Middleware
 */
class ConfigReader
{
    protected Config $config;

    public function __construct()
    {
        $this->config = $this->loadConfig();
    }

    public function getGlobalMiddleware(): Config
    {
        $config = $this->config;
        if ($config->has('middleware_global')) {
            return $config->get('middleware_global');
        }
        return new Config([]);
    }

    public function getGroupsMiddleware(): Config
    {
        $config = $this->config;
        if ($config->has('middleware_groups')) {
            return $config->get('middleware_groups');
        }
        return new Config([]);
    }

    protected function loadConfig(): Config
    {
        $baseConfig = new Config([]);
        $mergeConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/../config/middleware';

        $configClassReaders = [
            'php' => \Phalcon\Config\Adapter\Php::class,
            'php5' => \Phalcon\Config\Adapter\Php::class,
            'inc' => \Phalcon\Config\Adapter\Php::class,
            'ini' => \Phalcon\Config\Adapter\Ini::class,
            'json' => \Phalcon\Config\Adapter\Json::class,
            'yml' => \Phalcon\Config\Adapter\Yaml::class,
            'yaml' => \Phalcon\Config\Adapter\Yaml::class
        ];

        foreach ($configClassReaders as $extension => $classReader) {
            if (file_exists($mergeConfigPath . '.' . $extension)) {
                $baseConfig->merge(new $classReader($mergeConfigPath . '.' . $extension));
                return $baseConfig;
            }
        }
        return $baseConfig;
    }
}
