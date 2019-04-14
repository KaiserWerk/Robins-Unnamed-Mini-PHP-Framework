<?php

class TemplateHelper
{
    public static function render($template, $vars = [], $headerTemplate = null, $footerTemplate = null)
    {
        global $config;
        foreach ($config as $key => $value) {
            $$key = $value;
        }
        // $vars is always an array
        foreach ($vars as $key => $value) {
            $$key = $value;
        }
        
        if ($headerTemplate == null) {
            $headerTemplate = '/header';
        }
        if ($footerTemplate == null) {
            $footerTemplate = '/footer';
        }
        
        $headerTemplate = self::checkTemplateFilename($headerTemplate);
        $template = self::checkTemplateFilename($template);
        $footerTemplate = self::checkTemplateFilename($footerTemplate);
        
        if (!file_exists(TEMPLATEPATH . '/' . $headerTemplate)) {
            die('Header Template File ' . $headerTemplate . ' does not exist!');
        }
        if (!file_exists(TEMPLATEPATH . '/' . $template)) {
            die('Template File ' . $template . ' does not exist!');
        }
        if (!file_exists(TEMPLATEPATH . '/' . $footerTemplate)) {
            die('Header Template File ' . $footerTemplate . ' does not exist!');
        }
        
        require TEMPLATEPATH . '/' . $headerTemplate;
        require TEMPLATEPATH . '/' . $template;
        require TEMPLATEPATH . '/' . $footerTemplate;
        die;
    }
    
    public static function renderAdmin($template, $vars = [], $headerTemplate = null, $footerTemplate = null)
    {
        global $config;
        // $vars is always an array
        foreach ($vars as $key => $value) {
            $$key = $value;
        }
        
        if ($headerTemplate == null) {
            $headerTemplate = '/admin/header';
        }
        if ($footerTemplate == null) {
            $footerTemplate = '/admin/footer';
        }
        
        $headerTemplate = self::checkTemplateFilename($headerTemplate);
        $template = self::checkTemplateFilename($template);
        $footerTemplate = self::checkTemplateFilename($footerTemplate);
        
        if (!file_exists(TEMPLATEPATH . '/' . $headerTemplate)) {
            die('Header Template File ' . $headerTemplate . ' does not exist!');
        }
        if (!file_exists(TEMPLATEPATH . '/admin/' . $template)) {
            die('Template File ' . $template . ' does not exist!');
        }
        if (!file_exists(TEMPLATEPATH . '/' . $footerTemplate)) {
            die('Header Template File ' . $footerTemplate . ' does not exist!');
        }
        
        require TEMPLATEPATH . '/' . $headerTemplate;
        require TEMPLATEPATH . '/admin/' . $template;
        require TEMPLATEPATH . '/' . $footerTemplate;
        die;
    }
    
    private static function checkTemplateFilename($filename)
    {
        if (strpos($filename, '.tpl.php') === false) {
            return $filename . '.tpl.php';
        }
        return $filename;
    }
}