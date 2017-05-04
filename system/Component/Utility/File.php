<?php
/**
* BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
* Copyright (c) BabiPHP. (http://babiphp.org)
*
* Licensed under The GNU General Public License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
* @link          http://babiphp.org BabiPHP Project
* @package       system.component.utility
* @since         BabiPHP v 0.8.4
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* BabiPHP FileInfo Class.
* 
* Not edit this file
*
*/

    namespace BabiPHP\Component\Utility;

    use \Exception;
    
    class FileException extends Exception
    {
        public function __construct($message, $code)
        {
            parent::__construct($message, $code);
        }
    }

    class File
    {
        private $width;
        private $height;
        private $mime;
        private $size;
        private $extension;
        private $name;
        private $dir;

        public function __construct() {;}

        public function getWidth()
        {
            return $this->width;
        }

        public function getHeight()
        {
            return $this->height;
        }

        public function getMime()
        {
            return $this->mime;
        }

        public function getSize()
        {
            return $this->size;
        }

        public function getExtension()
        {
            return $this->extension;
        }

        public function getName()
        {
            return $this->name;
        }
        public function getDir()
        {
            return $this->dir;
        }

        public function loadInfo($fileName)
        {
            if (!file_exists($fileName) || !is_file($fileName))
                throw new FileException('Invalid file name', 0);
            if (!is_readable($fileName))
                 throw new FileException('File could not be read', 1);
           $info = pathinfo($fileName);
           $this->dir = $info['dirname'];
           $this->name = $info['basename'];
           if (key_exists('extension', $info)) {
                $this->extension = $info['extension'];
           }
           $dims = @getimagesize($fileName);
           $this->width = $dims[0];
           $this->height = $dims[1];
           $this->mime = $dims['mime'];
           $this->size = filesize($fileName);
            
        }

        public static function getFileExtension($fileName)
        {
            if (!is_string($fileName))
                throw new FileException('Invalid file name', 0);
            return pathinfo($fileName, PATHINFO_EXTENSION);
        }
    }
?>
