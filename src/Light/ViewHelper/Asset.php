<?php

namespace Light\ViewHelper;

use Light\Front;
use Light\ViewHelper;

/**
 * Class Asset
 * @package Light\ViewHelper
 */
class Asset extends ViewHelper
{
    /**
     * @param $assets
     * @return string
     */
    public function call($assets)
    {
        $assetsHtml = [];

        foreach ((array)$assets as $asset) {

            $assetParts = explode('.', explode('?', $asset)[0]);

            $assetsHtml[] = end($assetParts) == 'js' ? $this->js($asset) : $this->css($asset);
        }

        return implode("\n", $assetsHtml);
    }

    /**
     * @param string $uri
     * @return string
     */
    public function js(string $uri) : string
    {
        return '<script src="' . $this->prepareUnderscore($uri) . '"></script>';
    }

    /**
     * @param string $uri
     * @return string
     */
    public function css(string $uri) : string
    {
        return '<link rel="stylesheet" href="' . $this->prepareUnderscore($uri) . '" />';
    }

    /**
     * @param string $uri
     * @return string
     */
    public function prepareUnderscore(string $uri) : string
    {
        $config = Front::getInstance()->getConfig()['light']['asset'] ?? [
            'underscore' => false,
            'prefix' => ''
        ];

        if ($config['underscore']) {

            if (strpos($uri, '?') !== false) {
                $uri = $uri . '&_=' . microtime();
            }
            else {
                $uri = $uri . '?_=' . microtime();
            }
        }

        if (!empty($config['prefix'])) {

            if (substr($uri, 0, 2) != '//' && substr($uri, 0, 7) != 'http://' && substr($uri, 0, 8) != 'https://') {

                if (substr($uri, 0, 1) != '/') {
                    $uri = '/' . $uri;
                }

                $uri = $config['prefix'] . $uri;
            }
        }

        return $uri;
    }
}