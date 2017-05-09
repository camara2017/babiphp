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

	namespace BabiPHP\Component\Utility;

	/**
	 * Mailer PHP email creation and transport class.
	 * @author Lambirou (since v1.4) 18/09/2015
	 * @author Davis Peixoto v1.3
	 * @package BabiPHP
	 */
	class Mailer_old
	{
		const STRIP_RETURN_PATH = TRUE;
		
		private $to = NULL;
		private $subject = NULL;
		private $textMessage = NULL;
		private $altmessage = '';
		private $headers = NULL;
		
		private $recipients = array();
		private $cc = array();
		private $cco = array();
		private $from = NULL;
		private $replyTo = NULL;
		private $contentType = 'text/plain';
		private $charset = 'iso-8859-1';
		private $encoding = '8bit';
		private $attachments = array();
		private $errorInfo = '';

		protected $LE = PHP_EOL;
		protected $msgDate = '';
		protected $error_count = 0;
		protected $error_msg = array(
            'authenticate' => 'SMTP Error: Could not authenticate.',
            'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: data not accepted.',
            'empty_message' => 'Message body empty',
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
		
		public function __construct($to = NULL, $subject = NULL, $textMessage = NULL, $headers = NULL)
		{
			$this->to = $to;
			$this->recipients = $to;
			$this->subject = $subject;
			$this->textMessage = $textMessage;
			$this->headers = $headers;
			return $this;
		}

		/**
		 * validateAdress
		 * @param  string $address The email address to check
		 * @param  string $patternselect 
		 * @return boolean
		 */
		public function validateAddress($address, $patternselect = 'auto')
	    {
	        if (!$patternselect or $patternselect == 'auto') {
	            //Check this constant first so it works when extension_loaded() is disabled by safe mode
	            //Constant was added in PHP 5.2.4
	            if (defined('PCRE_VERSION')) {
	                //This pattern can get stuck in a recursive loop in PCRE <= 8.0.2
	                if (version_compare(PCRE_VERSION, '8.0.3') >= 0) {
	                    $patternselect = 'pcre8';
	                } else {
	                    $patternselect = 'pcre';
	                }
	            } elseif (function_exists('extension_loaded') and extension_loaded('pcre')) {
	                //Fall back to older PCRE
	                $patternselect = 'pcre';
	            } else {
	                //Filter_var appeared in PHP 5.2.0 and does not require the PCRE extension
	                if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
	                    $patternselect = 'php';
	                } else {
	                    $patternselect = 'noregex';
	                }
	            }
	        }
	        switch ($patternselect) {
	            case 'pcre8':
	                /**
	                 * Uses the same RFC5322 regex on which FILTER_VALIDATE_EMAIL is based, but allows dotless domains.
	                 * @link http://squiloople.com/2009/12/20/email-address-validation/
	                 * @copyright 2009-2010 Michael Rushton
	                 * Feel free to use and redistribute this code. But please keep this copyright notice.
	                 */
	                return (boolean)preg_match(
	                    '/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)' .
	                    '((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)' .
	                    '(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)' .
	                    '([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*' .
	                    '(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)' .
	                    '(?>(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?6)){7}' .
	                    '|(?!(?:.*[a-f0-9][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:' .
	                    '|(?!(?:.*[a-f0-9]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
	                    '|[1-9]?[0-9])(?>\.(?9)){3}))\])(?1)$/isD',
	                    $address
	                );
	            case 'pcre':
	                //An older regex that doesn't need a recent PCRE
	                return (boolean)preg_match(
	                    '/^(?!(?>"?(?>\\\[ -~]|[^"])"?){255,})(?!(?>"?(?>\\\[ -~]|[^"])"?){65,}@)(?>' .
	                    '[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*")' .
	                    '(?>\.(?>[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*"))*' .
	                    '@(?>(?![a-z0-9-]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)(?>\.(?![a-z0-9-]{64,})' .
	                    '(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)){0,126}|\[(?:(?>IPv6:(?>(?>[a-f0-9]{1,4})(?>:' .
	                    '[a-f0-9]{1,4}){7}|(?!(?:.*[a-f0-9][:\]]){8,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?' .
	                    '::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?))|(?>(?>IPv6:(?>[a-f0-9]{1,4}(?>:' .
	                    '[a-f0-9]{1,4}){5}:|(?!(?:.*[a-f0-9]:){6,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4})?' .
	                    '::(?>(?:[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4}):)?))?(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
	                    '|[1-9]?[0-9])(?>\.(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\])$/isD',
	                    $address
	                );
	            case 'html5':
	                /**
	                 * This is the pattern used in the HTML5 spec for validation of 'email' type form input elements.
	                 * @link http://www.whatwg.org/specs/web-apps/current-work/#e-mail-state-(type=email)
	                 */
	                return (boolean)preg_match(
	                    '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
	                    '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
	                    $address
	                );
	            case 'noregex':
	                //No PCRE! Do something _very_ approximate!
	                //Check the address is 3 chars or longer and contains an @ that's not the first or last char
	                return (strlen($address) >= 3
	                    and strpos($address, '@') >= 1
	                    and strpos($address, '@') != strlen($address) - 1);
	            case 'php':
	            default:
	                return (boolean)filter_var($address, FILTER_VALIDATE_EMAIL);
	        }
	    }
		
		/**
		 * Send
		 * @return boolean (true if the mail is sended)
		 */
		public function Send()
		{
			if (is_null($this->to)){
				$this->SetError('Must have at least one recipient.');
				throw new Exception("Must have at least one recipient.");
			}
			
			if (is_null($this->from)) {
				$this->SetError('Must have one, and only one sender set.');
				throw new Exception("Must have one, and only one sender set.");
			}
			
			if (is_null($this->subject)) {
				$this->SetError('Subject is empty.');
				throw new Exception("Subject is empty.");
			}
			
			if (is_null($this->textMessage)) {
				$this->SetError('Message is empty.');
				throw new Exception("Message is empty.");
			}
			
			$this->packHeaders();
			$sent = mail($this->to, $this->subject, $this->textMessage, $this->headers);

			if(!$sent)
			{
				$this->SetError('Server couldn\'t send the email.');
				throw new Exception('Server couldn\'t send the email.');
			}
			else
			{
				return true;
			}
		}
		
		public function From($name, $address)
		{
			$this->from = $name .'<'.$address.'>' . $this->LE;
			if (is_null($this->replyTo)) {
				$this->replyTo = $address. $this->LE;
			}
			return $this;
		}
		
		public function Recipient($name, $address)
		{
			$this->recipients .= (is_null($this->recipients)) ?  ("$name <$address>") : (", " . "$name <$address>");
			$this->to .= (is_null($this->to)) ?  $address : (", " . $address);
			return $this;
		}
		
		// New
		public function Address($address)
		{
			$addresses = array();
	        foreach ($addr as $address => $name) {
	            $addresses[] = $address;
	        }
	        return $type . ': ' . implode(', ', $addresses) . $this->LE;
			
			return $this;
		}
		
		public function CC($name, $address)
		{
			$this->cc .= (is_null($this->cc)) ? ("$name <$address>") : (", " . "$name <$address>");
			return $this;
		}
		
		public function CCO($name, $address)
		{
			$this->cco .= (is_null($this->cco)) ? ("$name <$address>") : (", " . "$name <$address>");
			return $this;
		}
		
		public function ReplyTo($address)
		{
			$this->replyTo = $address . PHP_EOL;
			return $this;
		}
		
		public function Subject($subject)
		{
			$this->subject = $subject;
			return $this;
		}
		
		public function Body($textMessage, $contentType = 'text/html', $charset = 'iso-8859-1')
		{
			$this->textMessage = $textMessage;
			$this->contentType = $contentType;
			$this->charset = $charset;
			return $this;
		}
		
		public function Attach($filePath)
		{
			$this->attachments[] = $filePath;
			return $this;
		}

		/**
	     * Add an address to one of the recipient arrays.
	     * Addresses that have been added already return false, but do not throw exceptions
	     * @param string $kind One of 'to', 'cc', 'bcc', 'ReplyTo'
	     * @param string $address The email address to send to
	     * @param string $name
	     * @throws phpmailerException
	     * @return boolean true on success, false if address already used or invalid in some way
	     * @access protected
	     */
	    protected function addAnAddress($kind, $address, $name = '')
	    {
	        if (!preg_match('/^(to|cc|bcc|Reply-To)$/', $kind))
	        {
	            $this->setError('Invalid recipient array: ' . $kind);
	            return false;
	        }

	        $address = trim($address);
	        $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim

	        if (!$this->validateAddress($address))
	        {
	            $this->setError($this->error_msg['invalid_address'] . ': ' . $address);
	            return false;
	        }

	        if ($kind != 'Reply-To')
	        {
	            if (!isset($this->all_recipients[strtolower($address)]))
	            {
	                array_push($this->$kind, array($address, $name));
	                $this->recipients[strtolower($address)] = true;
	                return true;
	            }
	        }
	        else
	        {
	            if (!array_key_exists(strtolower($address), $this->ReplyTo))
	            {
	                $this->ReplyTo[strtolower($address)] = array($address, $name);
	                return true;
	            }
	        }
	        return false;
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
	     * Create recipient headers.
	     * @access public
	     * @param string $type
	     * @param array $addr An array of recipient,
	     * where each recipient is a 2-element indexed array with element 0 containing an address
	     * and element 1 containing a name, like:
	     * array(array('joe@example.com', 'Joe User'), array('zoe@example.com', 'Zoe User'))
	     * @return string
	     */
	    public function AddrAppend($type, $addr)
	    {
	        $addresses = array();
	        foreach ($addr as $address) {
	            $addresses[] = $address;
	        }
	        return $type . ': ' . implode(', ', $addresses) . $this->LE;
	    }

		/**
		 * Date 
		 * @param date $date the email sended date
		 */
		public function Date($date)
		{
			$this->msgDate = $date;
			return $this;
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

		public static function prepareAttachment($path)
		{
	        $rn = "\r\n";

	        if (file_exists($path))
	        {
	            $finfo = finfo_open(FILEINFO_MIME_TYPE);
	            $ftype = finfo_file($finfo, $path);
	            $file = fopen($path, "r");
	            $attachment = fread($file, filesize($path));
	            $attachment = chunk_split(base64_encode($attachment));
	            fclose($file);

	            $msg = 'Content-Type: \'' . $ftype . '\'; name="' . basename($path) . '"' . $rn;
	            $msg .= "Content-Transfer-Encoding: base64" . $rn;
	            $msg .= 'Content-ID: <' . basename($path) . '>' . $rn;
				// $msg .= 'X-Attachment-Id: ebf7a33f5a2ffca7_0.1' . $rn;
	            $msg .= $rn . $attachment . $rn . $rn;
	            return $msg;
	        } else {
	            return false;
	        }
	    }

	    public static function sendMail($to, $subject, $content, $path = '', $cc = '', $bcc = '', $_headers = false)
	    {
	        $rn = "\r\n";
	        $boundary = md5(rand());
	        $boundary_content = md5(rand());

			// Headers
	        $headers = 'From: Mail System PHP <no-reply@domain.com>' . $rn;
	        $headers .= 'Mime-Version: 1.0' . $rn;
	        $headers .= 'Content-Type: multipart/related;boundary=' . $boundary . $rn;

	        //adresses cc and ci
	        if ($cc != '') {
	            $headers .= 'Cc: ' . $cc . $rn;
	        }
	        if ($bcc != '') {
	            $headers .= 'Bcc: ' . $cc . $rn;
	        }
	        $headers .= $rn;

			// Message Body
	        $msg = $rn . '--' . $boundary . $rn;
	        $msg.= "Content-Type: multipart/alternative;" . $rn;
	        $msg.= " boundary=\"$boundary_content\"" . $rn;

			//Body Mode text
	        $msg.= $rn . "--" . $boundary_content . $rn;
	        $msg .= 'Content-Type: text/plain; charset=ISO-8859-1' . $rn;
	        $msg .= strip_tags($content) . $rn;

			//Body Mode Html        
	        $msg.= $rn . "--" . $boundary_content . $rn;
	        $msg .= 'Content-Type: text/html; charset=ISO-8859-1' . $rn;
	        $msg .= 'Content-Transfer-Encoding: quoted-printable' . $rn;
	        if ($_headers) {
	            $msg .= $rn . '<img src=3D"cid:template-H.PNG" />' . $rn;
	        }
	        //equal sign are email special characters. =3D is the = sign
	        $msg .= $rn . '<div>' . nl2br(str_replace("=", "=3D", $content)) . '</div>' . $rn;
	        if ($_headers) {
	            $msg .= $rn . '<img src=3D"cid:template-F.PNG" />' . $rn;
	        }
	        $msg .= $rn . '--' . $boundary_content . '--' . $rn;

			//if attachement
	        if ($path != '' && file_exists($path)) {
	            $conAttached = self::prepareAttachment($path);
	            if ($conAttached !== false) {
	                $msg .= $rn . '--' . $boundary . $rn;
	                $msg .= $conAttached;
	            }
	        }
	        
			//other attachement : here used on HTML body for picture headers/footers
	        if ($_headers) {
	            $imgHead = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-H.PNG';
	            $conAttached = self::prepareAttachment($imgHead);
	            if ($conAttached !== false) {
	                $msg .= $rn . '--' . $boundary . $rn;
	                $msg .= $conAttached;
	            }
	            $imgFoot = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-F.PNG';
	            $conAttached = self::prepareAttachment($imgFoot);
	            if ($conAttached !== false) {
	                $msg .= $rn . '--' . $boundary . $rn;
	                $msg .= $conAttached;
	            }
	        }

	        $msg .= $rn . '--' . $boundary . '--' . $rn;

	        mail($to, $subject, $msg, $headers);
	    }
		
		/**
		 * packHeader set the header ok message
		 * @return void
		 */
		private function packHeaders()
		{
			if (!$this->headers)
			{
				if ($this->msgDate == '')
				{
		            $this->msgDate = $this->rfcDate();
		        }

		        $this->headers .= $this->headerLine('Date', $this->msgDate);
				$this->headers .= $this->headerLine('MIME-Version', '1.0');
				$this->headers .= $this->AddrAppend('To', $this->recipients);
				$this->headers .= $this->headerLine('From', $this->from);
				
				if (self::STRIP_RETURN_PATH !== TRUE) {
					$this->headers .= $this->AddrAppend('Reply-To', $this->replyTo);
					$this->headers .= $this->AddrAppend('Return-Path', $this->from);
				}
				
				if ($this->cc) {
					$this->headers .= $this->AddrAppend('Cc', $this->cc);
				}
				
				if ($this->cco) {
					$this->headers .= $this->AddrAppend('Bcc', $this->cco);
				}
				
				$str = '';
				
				if ($this->attachments)
				{
					$random_hash = md5(date('r', time()));
					$this->headers .= $this->headerLine('Content-Type', 'multipart/mixed; boundary=\PHP-mixed-'.$random_hash.'\\');

					$str .= '--PHP-mixed-'.$random_hash . $this->LE;
					$str .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
					$str .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
					$str .= $this->textMessage . $this->LE;
					
					$pos = strpos($this->textMessage, "<html>");
					
					if ($pos > 0)
					{
						$str .= $this->headerLine('Content-Type', 'multipart/alternative; boundary=PHP-alt-'.$random_hash.'\\');
						$str .= '--PHP-mixed-'.$random_hash . $this->LE;
						$str .= $str .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
						$str .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
						$str .= substr($this->textMessage, 0, $pos);
						$str .= $this->LE;
						$str .= '--PHP-mixed-'.$random_hash . $this->LE;
						$str .= $str .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
						$str .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
						$str .= substr($this->textMessage, $pos);
						$str .= '--PHP-mixed-'.$random_hash . '--'.$this->LE;
					}
					
					foreach ($this->attachments as $key => $value)
					{
						$mime_type = mime_content_type($value);
						//$mime_type = "image/jpeg";
						$attachment = chunk_split(base64_encode(file_get_contents($value)));
						$fileName = basename($value);
						$str .= '--PHP-mixed-'.$random_hash . $this->LE;
						$str .= $this->headerLine('Content-Type', $mime_type.'; name='.$fileName);
						$str .= $this->headerLine('Content-Disposition', 'attachment');
						$str .= $this->headerLine('Content-Transfer-Encoding', 'base64');
						$str .= $this->LE;
						$str .= $attachment;
						$str .= $this->LE;
					}

					$str .= '--PHP-mixed-'.$random_hash . '--'.$this->LE;
				}
				else
				{
					$this->headers .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
					$this->headers .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
					$str .= $this->textMessage . $this->LE;

					$pos = strpos($this->textMessage, "<html>");
					
					if ($pos > 0)
					{
						$random_hash = md5(date('r', time()));
						$this->headers .= $this->headerLine('Content-Type', 'multipart/alternative; boundary=PHP-alt-'.$random_hash.'\\');
						$str .= '--PHP-mixed-'.$random_hash . $this->LE;
						$str .= $str .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
						$str .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
						$str .= substr($this->textMessage, 0, $pos);
						$str .= $this->LE;
						$str .= '--PHP-mixed-'.$random_hash . $this->LE;
						$str .= $str .= $this->headerLine('Content-Type', $this->contentType.'; charset='.$this->charset);
						$str .= $this->headerLine('Content-Transfer-Encoding', $this->encoding);
						$str .= substr($this->textMessage, $pos);
						$str .= '--PHP-mixed-'.$random_hash . '--'.$this->LE;
					}
				}

				$this->textMessage = $str;
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
	        $this->errorInfo = $msg;
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

/* usage */
/*
$myMessage = "<html>...";
try {
	// minimal requirements to be set
	$dummy = new Mailer();
	$dummy->From("My Website", "contact@mywebsite.com");
	$dummy->Recipient("Holly","holly@email.com");
	$dummy->Subject("About stuff");
	$dummy->Body($myMessage);
	
	// options below are completely optional
	$dummy->Recipient("Marcus", "marcus@anothermail.org");
	$dummy->CC("Mr. Carlson", "manager@business.com");
	$dummy->CCO("Mr. X", "mistery@mindmail.com");
	$dummy->Attach('../files/file1.txt');
	
	// now we send it!
	$dummy->Send();
} catch (Exception $e) {
	echo $e->getMessage();
	exit(0);
}

echo "Success!";
*/
?>