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
 * @package       system.helpers
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Form Helper Class.
 * 
 * Not edit this file
 *
 */

    namespace BabiPHP\Component\Helper;

    class Form {

        public $name;
        public $attrs = array();

        public function __construct()
        {

        }

        public function start($attrs = array()) {
            $n_attrs = array(
                'name' => '',
                'method' => 'get',
                'action' => $_SERVER['REQUEST_URI'],
                'accept-charset' => 'utf8'
            );
            $attrs = array_merge($n_attrs, $attrs);

            return sprintf('<form %s >', $this->htmlattrs($attrs));
        }

        public function end(){
            return '</form>';
        }

        public function label($label, $required = false, $attrs = array()){
            return sprintf('<label %s>%s %s</label>', 
                $this->htmlattrs($attrs),
                $label,
                $required ? '<span class="required">*</span>' : ''
            );
        }

        public function htmlattrs($array, $filter = null, $exclude = true) {
            $attrs = array();
            foreach ($array as $key => $value) {
                if (!empty($filter) && ((!$exclude && !in_array($key, (array) $filter)) || 
                    ($exclude && in_array($key, (array) $filter)))) {
                    continue;
                }
                $attrs[] = sprintf('%s="%s"', $key, $value);
            }
            return implode(' ', $attrs);
        }

        public function input($type = 'text', $name, $value = '', $attrs = array()) {
            $n_attrs = array(
                'type' => $type,
                'name' => $name,
                'value' => $value
            );
            $attrs = array_merge($n_attrs, $attrs);

            return sprintf('<input %s ></input>', $this->htmlattrs($attrs));
        }

        public function get_input($type = 'text', $name, $value = '', $attrs = array()) {
            $n_attrs = array(
                'type' => $type,
                'name' => $name,
                'value' => $value
            );
            $attrs = array_merge($n_attrs, $attrs);

            return sprintf('<input %s ></input>', $this->htmlattrs($attrs));
        }

        public function checkbox($name, $value = 1, $checked = false, $attrs = array()) {
            if ($checked) {
                $attrs['checked'] = 'checked';
            }
            return $this->get_input('checkbox', $name, $value, $attrs);
        }

        public function file($name, $attrs = array()){
            return $this->get_input('file', $name, '', $attrs);
        }
        
        public function hidden($name, $value = '', $attrs = array()) {
            return $this->get_input('hidden', $name, $value, $attrs);
        }

        public function radios($name, $values, $attrs = array()) {
            foreach ($values as $value) {
                return $this->get_input('radio', $name, $value, $attrs).$value.' ';
            }
        }
    
        public function select($name, $options, $value = '', $attrs = array()) {

            $n_attrs = array(
                'name' => $name
            );
            $attrs = array_merge($n_attrs, $attrs);
            
            $html = sprintf('<select %s>', $this->htmlattrs($attrs));
            foreach ($options as $key => $text) {
                $html .= sprintf('<option value="%s"%s>%s</option>', 
                    $key, 
                    $key == $value ? ' selected="selected"' : '', 
                    $text
                );
            }
            $html .= '</select>';
            
            return $html;
        }

        public function textarea($name, $value, $attrs = array()){
            
            $n_attrs = array(
                'name' => $name
            );
            $attrs = array_merge($n_attrs, $attrs);

            return sprintf('<textarea %s>%s</textarea>', $this->htmlattrs($attrs), $value);
        }
    
        public function button($type = 'button', $name, $text, $attrs = array()) {

            $n_attrs = array(
                'type' => $type,
                'name' => $name
            );
            $attrs = array_merge($n_attrs, $attrs);
            
            return sprintf('<button %s>%s</button>', $this->htmlattrs($attrs), $text);
        }

        /*function render(){
                $output = '<form';
                $output .= '>';

                // finish building the output
                $output = $output . $contents . '</form>';

                echo $output;
        }
        */
    }
	
?>