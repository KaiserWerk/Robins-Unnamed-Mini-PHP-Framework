<?php

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

function _t($key)
{
    $locale = AuthHelper::getUserLocale();
    $translationFile = PROJECTPATH . '/app/Resources/translations/' . $locale . '.yml';
    if (!file_exists($translationFile)) {
        die('Translation file ' . $translationFile . ' does not exists');
    }
    try {
        $translation = Yaml::parseFile($translationFile);
    } catch (ParseException $e) {
        printf('Unable to parse the translation file file ' . $translationFile . '! %s', $e->getMessage());
        die;
    }
    
    return $translation[$key];
}