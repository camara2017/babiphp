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
* @package       system.core
* @since         BabiPHP v 0.7.5
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* BabiPHP Authentification Class File.
* 
* Not edit this file
*
*/

	namespace BabiPHP\Component\Utility;

	use BabiPHP\Component\Config\Security;
	use BabiPHP\Component\Config\Config;
    use BabiPHP\Component\Http\Request;
	use BabiPHP\Component\Utility\Session;
	
	class Auth
	{
		private $HashKey;
		private $SecurityKey;
		private $Request;
        private $CookiePath;

        private $app_provider;

        // User Roles
        private $roles = array(
        	'anonym' => 'ROLE_ANONYM',
        	'user' => 'ROLE_USER',
        	'admin' => 'ROLE_ADMIN',
        	'super_admin' => 'ROLE_SUPER_ADMIN'
        );

		// User default data
        private $user_session = array(
	        'user_id' => null,
	        'role' => 'ROLE_ANONYM',
	        'token' => null,
	        'data' => null
	    );

        // Password encoders algorythms
        private $Encoders = array(
        	0 => null,
        	1 => 'plaintext',
        	2 => 'sha512'
        );
        
        // Character type
		private $Chars = array(
			'int' => '0123456789',
			'string' => 'abcdefghijklmnopqrstuvwxyz',
			'alphanumeric' => 'abcdefghijklmnopqrstuvwxyz0123456789'
		);

		// Instance of this class
		private static $_instance;

		function __construct($request)
		{
			$this->Request = $request;
			$this->Set = new Set();
			$this->Session = Session::GetInstance();

			$this->HashKey = Security::get('Security_salt');
			$this->SecurityKey = Security::get('Security_cipherSeed');
			$this->Chars[0] = Security::get('Random_string');

            $this->setAuthName(Config::get('name'));

            self::$_instance = $this;
		}

		/**
		* GetInstance
		*/
		public static function getInstance()
		{
			return self::$_instance;
		}

		public function addRole($slug, $role)
		{
			if (!isset($this->roles[$slug])) {
				$this->roles[$slug] = $role;
			}
		}

		/**
		* Check
		* @return boolean
		*/
		public function Check()
		{
			return $this->Session->Check($this->app_provider);
		}

		/**
		* User
		* @param $user_id
		* @param $role
		* @param $token
		*/
		public function Create($id, $role = 'anonym', $token = null, $data = null)
		{
			$this->user_session = array(
				'user_id' => $this->setUserId($id),
				'role' => $this->setRole($role),
				'token' => $token,
				'data' => $data
			);

			$this->Session->Set($this->app_provider, $this->user_session);
		}

		public function setData($data)
		{
			$providers = $this->Session->Get($this->app_provider);
			$providers['data'] = $data;

			$this->Session->Set($this->app_provider, $providers);
		}

		/**
		* SetProvider
		* @param $provider
		*/
		public function setProvider($provider, $value)
		{
			$old_providers = $this->Session->Get($this->app_provider);
			$new_provider = array($provider => $value);
			$data = array_merge($old_providers, $new_provider);

			$this->Session->Set($this->app_provider, $data);
		}

		/**
		* GetProvider
		* @param $provider
		*/
		public function getProvider($provider)
		{
			$_d = $this->Session->Get($this->app_provider);

			return (isset($_d[$provider])) ? $_d[$provider] : null;
		}

		/**
		* CheckProvider
		* @param $provider
		* @return boolean
		*/
		public function checkProvider($provider)
		{
			$data = $this->Session->Get($this->app_provider);
			return isset($data[$provider]);
		}

		/**
		* SetToken
		*/
		public function setToken($token)
		{
			if($this->Check())
			{
				$_data = array('token' => $token);
				$this->Session->Set($this->app_provider, $_data);
			}
		}

		/**
		* SetUserId
		* @param $id
		*/
		private function setUserId($id = null)
		{
			if($id) {
				if(is_array($id)) return $this->Set->arrayToObject($id);
				elseif(is_object($id) || is_numeric($id)) return $id;
			}
			return null;
		}

		/**
		* SetRole
		* @param $role
		* @return role (in provider format)
		*/
		private function setRole($role)
		{
			$role = isset($this->roles[$role]) ? $this->roles[$role] : 'ROLE_ANONYM';
			return $role;
		}

		/**
		* Destroy
		*/
		public function Destroy()
		{
			$this->Session->Delete($this->app_provider);
		}

		/**
		* IsLogged
		* @return boolean
		*/
		public function isLogged()
		{
			if ($this->Session->Check($this->app_provider, 'user_id'))
			{
				if (!is_null($this->getProvider('user_id')) || $this->getProvider('user_id') != 0)
				{
					return true;
				}
			}
			return false;
		}

		/**
		* IsAnonym
		* @return boolean
		*/
		public function isAnonym()
		{
			$session = $this->Session->Get($this->app_provider);

			return ($session['role'] === 'ROLE_ANONYM') ? true : false;
		}

		/**
		* IsUser
		* @return boolean
		*/
		public function isUser()
		{
			$session = $this->Session->Get($this->app_provider);

			return ($session['role'] === 'ROLE_USER') ? true : false;
		}

		/**
		* IsAdmin
		* @return boolean
		*/
		public function isAdmin()
		{
			$admin_roles = Config::get('admin_role');

			foreach ($admin_roles as $_role)
			{
				if(!in_array($_role, $this->roles)) {
					return false;
				}
			}

			$_r = $this->Session->Get($this->app_provider);

			if(in_array($_r['role'], $admin_roles)) {
				return true;
			}
			
			return false;
		}

		/**
		* GetUser
		* @param (array) user connected providers
		*/
		public function getUser()
		{
			return $this->Session->Get($this->app_provider);
		}

		/**
		* Hash
		* @param $data to hash
		* @return string sha1 hashed
		*/
		public function Hash($data)
	    {
	    	return $this->Set->Hash($data);
		}

		private function setAuthName($app_name)
		{
			$app_name = strtolower($app_name);
			$app_name = str_replace(' ', '_', $app_name);

			$this->app_provider = $app_name.'_auth_providers';
		}

		/**
		* RandomString
		* @param $type
		* @param number of characters to return
		* @return rondom string
		*/
		private function Random($type = 'string', $length = 15)
		{
			$chars = $this->Chars[0];

			if($type == 'int') $chars = $this->Chars[$type];
			if($type == 'string') $chars = $this->Chars[$type];
			if($type == 'alphanumeric') $chars = $this->Chars[$type];

			$string = '';    
				
			for ($p = 0; $p < $length; $p++) {
				$string .= $chars[mt_rand(0, strlen($chars))];
			}
				
		    return $string;
		}
	}
?>