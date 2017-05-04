<?php

	namespace BabiPHP\Component\Database\Graphs;

	/**
	* GraphResponse
	*/
	class GraphResponse
	{
		private $data;

		function __construct($data)
		{
			$this->data = $data;
		}

		/**
	     * Returns true if the request is success or false if fail.
	     *
	     * @return boolean
	     */
		public function success()
		{
			return $this->data->success;
		}

		/**
	     * Returns the result of the request.
	     *
	     * @return mixed
	     */
		public function response()
		{
			return $this->data->response;
		}

		/**
	     * Returns an error if present.
	     *
	     * @return object|null
	     */
		public function error()
		{
			return $this->data->error;
		}

		/**
	     * Returns the sql request info
	     *
	     * @return object
	     */
		public function request()
		{
			return $this->data->request;
		}

		/**
		 * To edit response
		 * @param mixed $data
		 */
		public function addResponse($data = null)
		{
			if ($data) $this->data->response = $data;
		}
	}