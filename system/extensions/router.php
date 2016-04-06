<?php

/**
 * OpenGears Advanced Router
 *
 * @version 1.0
 * @author Denis Sedchenko
 * @date 31.10.2015
 *
 */
final class Router
{
    public static $routes = array();
    private static $params = array();
    public static $requestedUrl = '';

    /**
     * �������� �������
     * Add Route
     */
    public static function AddRoute($route, $destination=null) {
        if ($destination != null && !is_array($route)) {
            $route = array($route => $destination);
        }
        self::$routes = array_merge(self::$routes, $route);
    }

    /**
     * ��������� ���������� URL �� ����������
     * Split URL to elements
     */
    public static function SplitUrl($url) {
        return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * ������� ������������ URL
     * Current URL
     */
    public static function GetCurrentUrl() {
        return (self::$requestedUrl?:'/');
    }

    /**
     * ��������� ����������� URL
     * Dispatch received URL
     */
    public static function Dispatch($requestedUrl = null) {

        // ���� URL �� �������, ����� ��� �� REQUEST_URI
        if ($requestedUrl === null) {
            $uri = reset(explode('?', $_SERVER["REQUEST_URI"]));
            $requestedUrl = urldecode(rtrim($uri, '/'));
        }

        self::$requestedUrl = $requestedUrl;

        // ���� URL � ������� ��������� ���������
        if (isset(self::$routes[$requestedUrl])) {
            self::$params = self::SplitUrl(self::$routes[$requestedUrl]);
            return self::ExecuteAction();
        }

        foreach (self::$routes as $route => $uri) {
            // �������� wildcards �� ���. ���������
            if (strpos($route, ':') !== false) {
                $route = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $route));
            }

            if (preg_match('#^'.$route.'$#', $requestedUrl)) {
                if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {
                    $uri = preg_replace('#^'.$route.'$#', $uri, $requestedUrl);
                }
                self::$params = self::SplitUrl($uri);

                break; // URL ���������!
            }
        }
        return self::ExecuteAction();
    }

    /**
     * ������ ���������������� ��������/������/������ �����������
     * Execute controller or action
     */
    public static function ExecuteAction() {
        $controller = isset(self::$params[0]) ? self::$params[0]: DEFAULT_CONTROLLER;
        $action = isset(self::$params[1]) ? self::$params[1]: DEFAULT_ACTIVITY;
        $params = array_slice(self::$params, 2);

        // Register some values in system scope
        System::$Scope += array("controller"=>$controller,"activity"=>$action,"arguments"=>$params);

        // Fus Ro Dah!
        return System::Call($controller,$action);

    }
}