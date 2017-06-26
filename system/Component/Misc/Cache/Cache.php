<?php

    namespace BabiPHP\Component\Misc\Cache;
    
    class Cache
    {

        /**
         * Cache system
         *
         * @var object
         */
        private $system;

        /**
         *
         * @param [object] $system
         */
        public function __construct ($system)
        {
            $this->system = $system;
        }

        /**
         * Return $key value if exist
         *
         * @param string $key
         * @return string
         */
		public function get($key)
		{
			return $this->system->get($key);
		}
		
		public function set($key, $data, $lifetime=0, $compressed=0)
		{
			return $this->system->set($key, $data, $compressed, $lifetime);
		}
		
		public function delete($key)
		{
			return $this->system->delete($key);
		}
		
		public function clear()
		{
			return $this->system->flush();
		}
    }

?>