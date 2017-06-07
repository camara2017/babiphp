<?php

namespace BabiPHP\Component\Translation\Gettext\Extractors;

use BabiPHP\Component\Translation\Gettext\Translations;

/**
 * Class to get gettext strings from json files.
 */
class Jed extends PhpArray implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $content = json_decode($string);

        return PhpArray::handleArray($content, $translations);
    }
}
