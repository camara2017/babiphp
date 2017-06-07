<?php

namespace BabiPHP\Component\Translation\Gettext\Extractors;

use BabiPHP\Component\Translation\Gettext\Translations;
use BabiPHP\Component\View\Filesystem;
use BabiPHP\Component\View\Compilers\BladeCompiler;

/**
 * Class to get gettext strings from blade.php files returning arrays.
 */
class Blade extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        $bladeCompiler = new BladeCompiler(new Filesystem(), null);
        $string = $bladeCompiler->compileString($string);

        return PhpCode::fromString($string, $translations, $file);
    }
}
