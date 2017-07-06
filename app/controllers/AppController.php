<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @package       app.controllers
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * BabiPHP Application Main Controller
 */

use \BabiPHP\Core\Controller;

abstract class AppController extends Controller
{
    function __construct($request, $response)
    {
        parent::__construct($request, $response);

        $this->setUserLanguageDomain('home');
    }

    function renderError($e, $d)
    {
        $this->setLayout('error');

        $d['page'] = voidClass(['title'=>$e->title, 'type'=>'error']);
        $d['error'] = voidClass(['message' => $e->message]);

        $this->Render('/errors/index', $d);
    }

    protected function setUserLanguageDomain($domain)
    {
        $trans_locale = getBrowserLocale('en_US');
        $this->translate->Locale($trans_locale);
        $this->translate->Domain($domain);
    }

    function getJsonResponse()
    {
        return ['error'=>false, 'status'=>false, 'message'=>'', 'response'=>null];
    }
}