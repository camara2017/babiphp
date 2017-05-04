<?php
/**
* BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
* Copyright (c) BabiPHP. (http://babiphp.org)
*
* Licensed under The GNU General Public License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @author 		 Davis Peixoto v1.3
* @author 		 Lambirou (since v1.4) 18/09/2015
* @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
* @link          http://babiphp.org BabiPHP Project
* @package       system.core
* @since         BabiPHP v 0.8.3
* @license       http://www.gnu.org/licenses/ GNU License
*/

/**
* BabiPHP Mail Class.
* 
* Not edit this file
*
*/

	namespace BabiPHP\Component\Mail;

	/**
	 * Mailer PHP email creation and transport class.
	 * @author Lambirou (since v1.4) 18/09/2015
	 * @author Davis Peixoto v1.3
	 * @package BabiPHP
	 */
	class Mailer
	{
		const STRIP_RETURN_PATH = TRUE;
		
		protected $to = array();
		protected $subject = '';
		protected $message = '';
		protected $altmessage = '';
		protected $from = '';
		protected $replyTo = '';
		protected $cc = '';
		protected $bcc = '';
		protected $contentType = 'text/plain';
		protected $charset = 'iso-8859-1';
		protected $encoding = '8bit';
		protected $attachments = array();
		protected $MessageID = '';
		protected $headers = '';

		protected $boundaries = array();

		// protected $LE = PHP_EOL;
		protected $LE = "\r\n";
		protected $msgDate = '';
		protected $error_count = 0;
		protected $error_msg = array(
            'authenticate' => 'SMTP Error: Could not authenticate.',
            'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: data not accepted.',
            'empty_message' => 'Message body empty',
            'empty_attach' => 'Attachment empty',
            'notfound_attach' => 'Attachment not found to path',
            'encoding' => 'Unknown encoding: ',
            'execute' => 'Could not execute: ',
            'file_access' => 'Could not access file: ',
            'file_open' => 'File Error: Could not open file: ',
            'from_failed' => 'The following From address failed: ',
            'instantiate' => 'Could not instantiate mail function.',
            'invalid_address' => 'Invalid address',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address' => 'You must provide at least one recipient email address.',
            'recipients_failed' => 'SMTP Error: The following recipients failed: ',
            'signing' => 'Signing Error: ',
            'smtp_connect_failed' => 'SMTP connect() failed.',
            'smtp_error' => 'SMTP server error: ',
            'variable_set' => 'Cannot set or reset variable: ',
            'extension_missing' => 'Extension missing: '
        );

		public $ErrorInfo = array();

		// SMTP
		public $Smtp = false;
		public $SmtpSecure = '';
		public $Host = 'localhost';
		public $Port = 25;
		public $Username = '';
		public $Password = '';
		protected $pear_mail;
		
		/**
		 * __construct
		 */
		public function __construct()
		{
			require_once COMPONENT.'mail/Mail.php';
		}

		/**
		 * From
		 * @param string $name  sender name
		 * @param string $address sender address
		 */
		public function From($address, $name)
		{
			if($this->validateAddress($address))
			{
				$this->from = $name .'<'.$address.'>';

				if ($this->replyTo == '') 
				{
					$this->replyTo = $this->from;
				}

				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}

		/**
		 * ReplyTo
		 * @param string $address email reply address
		 */
		public function ReplyTo($address, $name)
		{
			if($this->validateAddress($address))
			{
				$this->replyTo = $name .'<'.$address.'>';
				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}

		/**
		 * CC
		 * @param string $address email reply address
		 */
		public function CC($address, $name)
		{
			if($this->validateAddress($address))
			{
				$this->cc = $name .'<'.$address.'>';
				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}

		/**
		 * CCO
		 * @param string $address email reply address
		 */
		public function CCO($address, $name)
		{
			if($this->validateAddress($address))
			{
				$this->bcc = $name .'<'.$address.'>';
				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}

		public function Address($addr)
		{
			if(is_array($addr))
			{
				foreach($addr as $address => $name)
				{
					$this->to[] = $name .'<'.$address.'>';
				}
				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}

		/**
		 * SingleTo
		 * @param string $name  sender name
		 * @param string $address sender address
		 */
		public function SingleTo($address, $name)
		{
			if($this->validateAddress($address))
			{
				$this->to[] = $name .'<'.$address.'>';
				return $this;
			}
			else $this->SetError($this->error_msg['invalid_address']);
		}
		
		/**
		 * Subject
		 * @param string $subject email subject
		 */
		public function Subject($subject)
		{
			$this->subject = $subject;
			return $this;
		}
		
		/**
		 * Body
		 * @param string $textMessage email body
		 * @param string $contentType
		 * @param string $charset
		 */
		public function Body($message, $contentType = 'text/html', $charset = 'iso-8859-1')
		{
			if(!empty($message))
			{
				$this->message = $message;
				$this->contentType = $contentType;
				$this->charset = $charset;
				return $this;
			}
			else $this->SetError($this->error_msg['empty_message']);
		}

		public function AltBody($message)
		{
			if(!empty($message))
			{
				$this->altmessage = $message;
				return $this;
			}
			else $this->SetError($this->error_msg['empty_message']);
		}

		public function Attach($attachs)
		{
			if(is_array($attachs))
			{
				if(!empty($attachs))
				{
					foreach($attachs as $path => $name)
					{
						$this->AttachFile($path, $name);
					}

					return $this;
				}
				else $this->SetError($this->error_msg['empty_attach']);
			}
			else $this->SetError('Attachment not send correctly.');
		}

		public function AttachFile($path, $name = null)
		{
			if ($path != '' && file_exists($path))
	        {
				if(is_null($name)) $name = basename($path);

				$this->attachments[] = $this->prepareAttachment($path, $name);
				return $this;
			}
			else $this->SetError($this->error_msg['notfound_attach'].' - '.$name);
		}

		/**
		 * ContentType
		 * @param string $contentType type of content of the message
		 */
		public function ContentType($contentType)
		{
			$this->contentType = $contentType;
			return $this;
		}

		/**
		 * Charset
		 * @param string $charset message charset
		 */
		public function Charset($charset)
		{
			$this->charset = $charset;
			return $this;
		}

		/**
		 * Encoder
		 * @param string $encode message encoding
		 */
		public function Encoder($encode)
		{
			$this->encoding = $encode;
			return $this;
		}

		/**
		 * validateAdress
		 * @param  string $address The email address to check
		 * @param  string $patternselect 
		 * @return boolean
		 */
		public function validateAddress($address)
	    {
	        return filter_var($address, FILTER_VALIDATE_EMAIL);
	    }

	    /**
	     * Return an RFC 822 formatted date.
	     * @access public
	     * @return string
	     * @static
	     */
	    public  function rfcDate()
	    {
	        // Set the time zone to whatever the default is to avoid 500 errors
	        // Will default to UTC if it's not set properly in php.ini
	        date_default_timezone_set(@date_default_timezone_get());
	        return date('D, j M Y H:i:s O');
	    }

		/**
	     * Format a header line.
	     * @access public
	     * @param string $name
	     * @param string $value
	     * @return string
	     */
	    public function HeaderLine($name, $value)
	    {
	        return $name . ': ' . $value . $this->LE;
	    }

	    /**
	     * Return a formatted mail line.
	     * @access public
	     * @param string $value
	     * @return string
	     */
	    public function textLine($value)
	    {
	        return $value . $this->LE;
	    }

	    protected function setHeader()
	    {
	    	if (!$this->headers)
	    	{
	    		$boundary = md5(microtime());
	    		$this->boundaries['content'] = $boundary;
	    		$this->MessageID = $boundary;

	    		if ($this->msgDate == '')
				{
		            $this->msgDate = $this->rfcDate();
		        }

		    	$headers = $this->headerLine('Date', $this->msgDate);
				$headers .= $this->headerLine('From', $this->from);
				$headers .= $this->headerLine('Reply-To', $this->replyTo);

				if ($this->cc != '') $headers .= $this->headerLine('Cc', $this->cc);
				if ($this->bcc != '') $headers .= $this->headerLine('Bcc', $this->bcc);

				$headers .= $this->headerLine('Message-ID', '<'.$this->MessageID.'@'.$_SERVER['HTTP_HOST'].'>');
				$headers .= $this->headerLine('X-Mailer', join('', array_slice(explode('\\', __CLASS__), -1)).' PHP/'.phpversion());
		    	$headers .= $this->headerLine('MIME-Version', '1.0');
				
				if($this->attachments)
	        	{
					$headers .= $this->headerLine('Content-Type', 'multipart/mixed;'.$this->LE."\tboundary=\"$boundary\"");
				}
				else
				{
					$headers .= $this->headerLine('Content-Type', 'multipart/alternative;'.$this->LE."\tboundary=\"$boundary\"");
				}

				$this->headers = $headers;
			}

			return $headers;
	    }

	    protected function setBody($content, $altmessage)
	    {
	    	$boundary = $this->boundaries['content'];
	    	$msg = '';

	    	if($this->attachments)
	    	{
	    		$msg .= $this->LE."--".$boundary.$this->LE;
	    		$msg .= $this->headerLine('Content-Type', 'multipart/alternative;'.$this->LE."\tboundary=\"$boundary\"");
	    	}

	    	if($altmessage)
	    	{
	        	$msg .= $this->LE."--".$boundary.$this->LE;
		        $msg .= $this->headerLine('Content-Type', 'text/plain; charset=' . $this->charset);
	        	$msg .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
		        $msg .= $this->LE . strip_tags($altmessage) . $this->LE;
	        }

	    	$msg .= $this->LE."--".$boundary.$this->LE;
	        $msg .= $this->headerLine('Content-Type', $this->contentType.'; charset=' . $this->charset);
	        $msg .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
	        $msg .= $this->LE . $content . $this->LE;

	        $msg .= $this->LE."--".$boundary."--".$this->LE;

	        if($this->attachments)
	        {
	    		$boundary_attach = $this->boundaries['attach'];

	        	foreach($this->attachments as $attach_file)
	        	{
	        		$msg .= $attach_file;
	        	}

				$msg .= $this->LE."--".$boundary_attach."--".$this->LE;
			}

	        return $msg;
	    }

	    protected function prepareAttachment($path, $name)
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, $path);
            $file = fopen($path, "r");
            $attachment = fread($file, filesize($path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);

            if(!isset($this->boundaries['attach']))
            {
            	$this->boundaries['attach'] = md5(uniqid(time()));
            }

            $boundary_attach = $this->boundaries['attach'];

	        $msg = $this->LE."--".$boundary_attach.$this->LE;
            $msg .= $this->headerLine('Content-Type', '\''.$ftype.'\'; name="'.$name.'"');
            $msg .= $this->headerLine('Content-Transfer-Encoding', 'base64');
	        $msg .= $this->headerLine('Content-Disposition', "attachment; filename=".$name."");
            $msg .= $this->headerLine('Content-ID', '<'.$name.'>');
            $msg .= $this->LE.$attachment.$this->LE.$this->LE;
            return $msg;
		}

	    public function Send()
	    {
			$result = false;

	    	if (count($this->to) == 0) $this->SetError($this->error_msg['provide_address']);
	    	if ($this->from == '') $this->SetError('Must have one, and only one sender set.');
			if ($this->subject == '') $this->SetError('Subject is empty.');
			if ($this->message == '') $this->SetError('Message is empty.');

	    	if ($this->error_count == 0)
	    	{
	    		$subject = $this->subject;
	    		$headers = $this->setHeader();
	    		$message = $this->setBody($this->message, $this->altmessage);

	    		foreach($this->to as $to)
    			{
    				$result = $this->mailSend($to, $subject, $message, $headers);
    			}
	        }

	        return $result;
	    }

	    protected function mailSend($to, $subject, $message, $headers)
	    {
	    	$send = mail($to, $subject, $message, $headers);

	    	if($send)
    		{
    			$this->ErrorInfo = array();
    			$this->error_count = 0;
    			return true;
    		}
	    	else
	    	{
	    		$this->SetError('Server couldn\'t send the email.');
	    		return $this->ErrorInfo;
	    	}
	    }

		/**
	     * Add an error message to the error container.
	     * @access protected
	     * @param string $msg
	     * @return void
	     */
	    protected function SetError($msg)
	    {
	        $this->error_count++;
	        $this->ErrorInfo[] = $msg;
	    }

	    /**
	     * Check if an error occurred.
	     * @access public
	     * @return boolean True if an error did occur.
	     */
	    public function IsError()
	    {
	        return ($this->error_count > 0);
	    }

	}

?>