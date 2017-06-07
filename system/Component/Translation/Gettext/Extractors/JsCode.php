<?php

namespace BabiPHP\Component\Translation\Gettext\Extractors;

use BabiPHP\Component\Translation\Gettext\Translations;
use BabiPHP\Component\Translation\Gettext\Utils\JsFunctionsScanner;

/**
 * Class to get gettext strings from javascript files.
 */
class JsCode extends Extractor implements ExtractorInterface
{
    public static $functions = array(
        '__' => '__',
        'n__' => 'n__',
        'p__' => 'p__',
    );

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $functions = new JsFunctionsScanner($string);
        $functions->saveGettextFunctions(self::$functions, $translations, $file);
    }
}
