<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @package       app.config
 * @since         BabiPHP v 0.7.9
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Firewall File.
 * 
 * Not edit this file
 *
 */

    use \BabiPHP\Component\Config\Config;
    use \BabiPHP\Component\Utility\Set;

    $system_firewall = Config::get('system_firewall');
    $system_access = Config::get('access_control');
    $login_route = Config::get('login_form');

    if(!$this->Auth->Check())
    {
        $anonym_token = $this->Set->Hash(microtime());
        $this->Auth->Create(null, 'anonym', $anonym_token);
    }

    $request = $this->Request;
    $access = [];
    $i = 0;

    foreach ($system_access as $role => $ressources)
    {
        $access[$i]['role'] = $role;
        $access[$i]['ressource'] = $ressources;
        $i++;
    }

    foreach ($access as $key => $value)
    {
        $allowed_roles = explode('|', $value['role']);
        $ressources = explode('|', $value['ressource']);

        if(in_array($request->getController(), $ressources))
        {
            $user = $this->Auth->GetUser();

            if(!in_array($user['role'], $allowed_roles))
            {
                $route = explode('#', $login_route);

                if($request->getAction() != $route[1])
                {
                    $this->Response->Redirect($route[0].'/'.$route[1]);
                }
            }
        }
    }

    /**
    * Secure system directory
    */
    if ( in_array($request->getController(), $system_firewall)) {
        $this->Response->redirect410();
    }
	
?>