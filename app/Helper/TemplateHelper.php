<?php

class TemplateHelper
{
    public static function render(string $template, ?array $vars = [], $headerTemplate = false, $footerTemplate = false)
    {
        global $config;
        $title = (isset($title)) ? $title : $config->site->title;
        
        // directly access config keys in templates
        foreach ($config as $key => $value) {
            $$key = $value;
        }
        
        // basic breadcrumb
        $bc = explode('/', $_SERVER['REQUEST_URI']);
        unset($bc[0]);
        $bc = array_values($bc);
        
        // $vars is always an array
        foreach ($vars as $key => $value) {
            $$key = $value;
        }
        
        // header template (optional)
        if ($headerTemplate !== null) {
            if ($headerTemplate === false) {
                $headerTemplate = '/include/header';
            }
            $headerTemplate = self::checkTemplateFilename($headerTemplate);
            $headerTemplatePath = TEMPLATEPATH . '/' . $headerTemplate;
            if (!file_exists($headerTemplatePath)) {
                die('Header Template File ' . $headerTemplate . ' does not exist!');
            }
            include $headerTemplatePath;
        }
        
        // body template (required)
        $template = self::checkTemplateFilename($template);
        $templatePath = TEMPLATEPATH . '/' . $template;
        if (!file_exists($templatePath)) {
            die('Template File ' . $template . ' does not exist!');
        }
        include $templatePath;
        
        // footer template (optional)
        if ($footerTemplate !== null) {
            if ($footerTemplate === false) {
                $footerTemplate = '/include/footer';
            }
            $footerTemplate = self::checkTemplateFilename($footerTemplate);
            $footerTemplatePath = TEMPLATEPATH . '/' . $footerTemplate;
            if (!file_exists($footerTemplatePath)) {
                die('Header Template File ' . $footerTemplate . ' does not exist!');
            }
            include $footerTemplatePath;
        }
        
        exit;
    }
    
    private static function checkTemplateFilename($filename)
    {
        if (substr($filename, -8) != '.tpl.php') {
            return $filename . '.tpl.php';
        }
        return $filename;
    }
    
    public static function includeTemplate($template, $vars = [])
    {
        foreach ($vars as $key => $value) {
            $$key = $value;
        }
        $filename = self::checkTemplateFilename($template);
        $filepath = TEMPLATEPATH . '/' . $filename;
        if (!file_exists($filepath)) {
            die('include template ' . $filepath . ' does not exist!');
        }
        include $filepath;
    }
    
    public static function getTemplateContent($template)
    {
        $filename = self::checkTemplateFilename($template);
        $filepath = TEMPLATEPATH . '/' . $filename;
        if (file_exists($filepath)) {
            return file_get_contents($filepath);
        }
        return false;
    }
    
    /**
     * Insert variables into an email template and
     * returns the complete code.
     *
     * @param $body
     * @param array $params
     * @return string
     */
    public static function insertValues($body, $params = [])
    {
        $body = str_replace('{support_email}', Helper::getSetting('admin_settings'), $body);
        $body = str_replace('{support_user}', Helper::getSetting('site_title'), $body);
        $body = str_replace('{host}', 'http'.(Helper::isSSL() ? 's' : '').'://'.$_SERVER['HTTP_HOST'], $body);
        $body = str_replace('{os_and_browser}', Helper::getOSAndBrowser(), $body);
        $body = str_replace('{ip_address}', Helper::getIP(), $body);
        
        if (!isset($params['tracking_token'])) {
            $body = preg_replace('#<!--et-->.*<!--/et-->#m', '', $body);
        }
        
        foreach ($params as $key => $value) {
            $body = str_replace('{'.$key.'}', $value, $body);
        }
        
        return $body;
    }
}