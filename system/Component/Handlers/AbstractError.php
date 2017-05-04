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
 * @package       system.component.handlers
 * @since         BabiPHP v 0.8.8
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Component\Handlers;

    use BabiPHP\Component\Config\Config;
    use BabiPHP\Component\Http\Request;

    /**
     * Abstract Slim application error handler
     */
    abstract class AbstractError
    {
        /**
         * @var bool
         */
        protected $display_error_details;

        /**
         * Known handled content types
         *
         * @var array
         */
        protected $knownContentTypes = [
            'application/json',
            'application/xml',
            'text/xml',
            'text/html',
        ];

        /**
         * Constructor
         *
         * @param bool $display_error_details Set to true to display full details
         */
        public function __construct()
        {
            $this->display_error_details = Config::get('display_error_details');
        }

        /**
         * Determine which content type we know about is wanted using Accept header
         *
         * Note: This method is a bare-bones implementation designed specifically for
         * Slim's error handling requirements. Consider a fully-feature solution such
         * as willdurand/negotiation for any other situation.
         *
         * @param Request $request
         * @return string
         */
        protected function determineContentType(Request $request)
        {
            $acceptHeader = $request->getHeaderLine('Accept');
            $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $this->knownContentTypes);

            if (count($selectedContentTypes)) {
                return current($selectedContentTypes);
            }

            // handle +json and +xml specially
            if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
                $mediaType = 'application/' . $matches[1];
                if (in_array($mediaType, $this->knownContentTypes)) {
                    return $mediaType;
                }
            }

            return 'text/html';
        }

        /**
         * Write to the error log if display_error_details is false
         *
         * @param \Exception|\Throwable $throwable
         *
         * @return void
         */
        protected function writeToErrorLog($throwable)
        {
            if ($this->display_error_details) {
                return;
            }

            $message = 'BabiPHP Application Error:' . PHP_EOL;
            $message .= $this->renderThrowableAsText($throwable);

            while ($throwable = $throwable->getPrevious()) {
                $message .= PHP_EOL . 'Previous error:' . PHP_EOL;
                $message .= $this->renderThrowableAsText($throwable);
            }

            $message .= PHP_EOL . 'View in rendered output by enabling the "display_error_details" setting.' . PHP_EOL;

            $this->logError($message);
        }

        /**
         * Render error as Text.
         *
         * @param \Exception|\Throwable $throwable
         *
         * @return string
         */
        protected function renderThrowableAsText($throwable)
        {
            $text = sprintf('Type: %s' . PHP_EOL, get_class($throwable));

            if ($code = $throwable->getCode()) {
                $text .= sprintf('Code: %s' . PHP_EOL, $code);
            }

            if ($message = $throwable->getMessage()) {
                $text .= sprintf('Message: %s' . PHP_EOL, htmlentities($message));
            }

            if ($file = $throwable->getFile()) {
                $text .= sprintf('File: %s' . PHP_EOL, $file);
            }

            if ($line = $throwable->getLine()) {
                $text .= sprintf('Line: %s' . PHP_EOL, $line);
            }

            if ($trace = $throwable->getTraceAsString()) {
                $text .= sprintf('Trace: %s', $trace);
            }

            return $text;
        }

        /**
         * Wraps the error_log function so that this can be easily tested
         *
         * @param $message
         */
        protected function logError($message)
        {
            error_log($message);
        }
    }
