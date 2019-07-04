<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class TranslationHelper
{
    /**
     * Returns the translation for a key
     *
     * @param $string
     * @param bool $return
     *
     * @return string
     */
    public static function _t($string, $return = false)
    {
        $locale = AuthHelper::getUserLocale();
        
        $translationFile = TRANSLATIONDIR . '/' . $locale . '.yml';
        if ( !file_exists( $translationFile ) ) {
            $translationFile = TRANSLATIONDIR . '/en.yml';
        }
    
        try {
            $trans = Yaml::parseFile($translationFile);
        } catch (ParseException $e) {
            printf('Unable to parse the YAML file! %s', $e->getMessage());
            die;
        }
        
        if ($trans !== false) {

            if (array_key_exists($string, $trans)) {
                if ($return) {
                    return $trans[$string];
                } else {
                    echo $trans[$string];
                    return '';
                }
            } else {
                $nf = 'Translation &quot;' . $string . '&quot; not found!';
                if ($return) {
                    return $nf;
                } else {
                    echo $nf;
                }
            }
        }
        return 'Translation (file) not found!';
    }
}