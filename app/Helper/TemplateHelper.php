<?php

class TemplateHelper
{
    public static function render($template, $vars = [], $headerTemplate = null, $footerTemplate = null)
    {
        global $config;
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
        
        if (!file_exists(templateDir() . '/' . $headerTemplate)) {
            die('Header Template File ' . $headerTemplate . ' does not exist!');
        }
        if (!file_exists(templateDir() . '/' . $template)) {
            die('Template File ' . $template . ' does not exist!');
        }
        if (!file_exists(templateDir() . '/' . $footerTemplate)) {
            die('Header Template File ' . $footerTemplate . ' does not exist!');
        }
        
        require templateDir() . '/' . $headerTemplate;
        require templateDir() . '/' . $template;
        require templateDir() . '/' . $footerTemplate;
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
        
        if (!file_exists(templateDir() . '/' . $headerTemplate)) {
            die('Header Template File ' . $headerTemplate . ' does not exist!');
        }
        if (!file_exists(templateDir() . '/admin/' . $template)) {
            die('Template File ' . $template . ' does not exist!');
        }
        if (!file_exists(templateDir() . '/' . $footerTemplate)) {
            die('Header Template File ' . $footerTemplate . ' does not exist!');
        }
        
        require templateDir() . '/' . $headerTemplate;
        require templateDir() . '/admin/' . $template;
        require templateDir() . '/' . $footerTemplate;
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