<?php

namespace Sendit;

use Sendit\Exception\Connection;

/**
 * Send It!
 *
 * @link      https://github.com/nicodiaz/sendit/ for the canonical source repository
 * @copyright Copyright (c) 2013 Nicolás Díaz País. (http://www.nicodp.com.ar)
 * @license   http://www.gnu.org/licenses/lgpl-2.1.html
 * 
 * @author nicodiaz
 * @package Sendit
 * @version 1.0.4
 */
class Sendit
{
	
	private $_config;

	/**
	 * The constructor of the class receive an array containing the following indexes:
	 * 
	 * 'host': database the register the emails
	 * 'port': the port of the database
	 * 'user': the username of the database
	 * 'password': the password of the database
	 * 'dbname': the name of the database
	 * 
	 * 'email_host': the smtp (IP or host) where is the smtp server
	 * 'email_ssl': the security of the smtp (i.e. tls)
	 * 'email_port': the port to connect 
	 * 'email_username': the username to log in
	 * 'email_password': the password to login
	 * 'email_from_email': the email that will be used in the "from" field
	 * 'email_from_name': The name that will be used in the "from" field
	 * 
	 * 'mock_test_mail': If the mail must be send or not (mocked by tests)
	 * 
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$keys1 = array(
			'host', 
			'port', 
			'user', 
			'password', 
			'dbname', 
			'email_host', 
			'email_ssl', 
			'email_port', 
			'email_username', 
			'email_password', 
			'email_from_email', 
			'email_from_name',
			'mock_test_mail'
		);
		
		$this->_config = array_fill_keys($keys1, 'value');
		
		$keys2 = array_keys($config); // the user provided keys

		$diff = array_merge(array_diff_key($keys1, $keys2), array_diff_key($keys2, $keys1));
		
		if (! empty($diff))
		{
			throw new \InvalidArgumentException(
			"The config array in constructor is not complete (see the documentation of the constructor)");
		}
		
		// If reach here, everything is fine
		$this->_config = $config;
	}

	/**
	 * 
	 * @var \PDO
	 */
	protected $_pdoConnection;

	/**
	 * Function to create (or retrieve) the current connection to the DB
	 * 
	 * @throws Connection
	 * @return \PDO
	 */
	protected function getConnection()
	{
		if (empty($this->_pdoConnection))
		{
			$result = $this->initConnection();
			
			if (is_string($result))
			{
				throw new Connection($result);
			}
			
			// If reach here, everything was fine
			$this->_pdoConnection = $result;
		}
		
		return $this->_pdoConnection;
	}

	public function setConnection(\PDO $conn)
	{
		$this->_pdoConnection = $conn;
	}

	/**
	 * Process the emails that are stored in the datasource and send them
	 * 
	 */
	public function processQueue()
	{
		$conn = $this->getConnection();
		
		$stmt = $conn->query(
		'
			SELECT e.*, t.subject, t.body 
			FROM emails e INNER JOIN types t ON e.typeId = t.id 
			WHERE sent = 0
		');
		
		$data = $stmt->fetch();
		while (! empty($data))
		{
			$data['body'] = $this->_replaceBodyParams($data['body'], $data['params']);
			
			$result = $this->sendMail($data['email'], $data['subject'], $data['body']);
			
			// If success, update the email as sent
			if ($result == true)
			{
				$conn->prepare('UPDATE emails SET sent = 1 WHERE id = :emailId')->execute(
				array(
					':emailId' => $data['id']
				));
			}
			
			// Next value
			$data = $stmt->fetch();
		}
	}
	
	
	/**
	 * Function to replace in the body the params encoded. 
	 * 
	 * Assumed that the params are json_encoded and replace the marks in the body as __?0__, __?1__, etc.
	 * 
	 * @param string $body
	 * @param string $paramsEncoded
	 */
	private function _replaceBodyParams($body, $paramsEncoded)
	{
		// Preconditions
		if (empty($paramsEncoded))
		{
			return $body;
		}
		
		$params = json_decode($paramsEncoded);
		
		if (! empty($params) && is_array($params))
		{
			for ($i = 0; $i < count($params); $i++) 
			{
				$mark = '__?' . $i . '__';
				$body = str_replace($mark, $params[$i], $body);
			}
		}
		
		return $body;
	}

	/**
	 * Internal funcion to construct and send the email with PHPMailer
	 * 
	 * Supports HTML body
	 * 
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * 
	 * @return boolean
	 */
	protected function sendMail($to, $subject, $body)
	{
		$mail = new \PHPMailer();
		
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->SMTPSecure = $this->_config['email_ssl']; // sets the prefix to the servier
		$mail->Host = $this->_config['email_host']; // sets the SMTP server
		$mail->Port = $this->_config['email_port']; // sets the SMTP port
		$mail->Username = $this->_config['email_username']; // email server username
		$mail->Password = $this->_config['email_password']; // email server password
		

		$mail->SetFrom($this->_config['email_from_email'], $this->_config['email_from_name']);
		
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		
		$mail->AddAddress($to);
		
		//
		return (array_key_exists('mock_test_mail', $this->_config) && $this->_config['mock_test_mail'] == true)? true:$mail->Send();
	}

	/**
	 * Add an email to the queue to be processed by the cron job
	 * Returns true on success or false on failure.
	 * 
	 * This function doesn't work in transactional way, that must be implemented
	 * 
	 * The params is replaced in the type text, with the placeholders '__?0__', '__?1__'
	 * 
	 * @param string $email
	 * @param int $type
	 * @param unknown_type $params
	 * 
	 * @throws \InvalidArgumentException
	 * 
	 * @return bool 
	 */
	public function queueEmail($email, $type = 1, $params = array())
	{
		// Preconditions
		if (empty($email) || empty($type) || $type <= 0)
		{
			throw new \InvalidArgumentException("Error adding an email to the queue: is empty or the type doesn't exists");
		}
		
		$conn = $this->getConnection();
		
		$params = json_encode($params);
		
		$st = $conn->prepare('INSERT INTO emails (typeId, email, params) VALUES(:typeId, :email, :params)');
		
		return $st->execute(array(
			':typeId' => $type, 
			':email' => $email,
			':params' => $params,
		));
	}

	/**
	 * Function to create the (initial) connection to MySQL Database
	 *
	 * @return void
	 */
	protected function initConnection()
	{
		$dsn = 'mysql:host=' . $this->_config['host'] . ';port=' . $this->_config['port'] . ';dbname=' . $this->_config['dbname'];
		$username = $this->_config['user'];
		$password = $this->_config['password'];
		$options = array(
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		);
		
		$result = false;
		
		try
		{
			$result = new \PDO($dsn, $username, $password, $options);
		}
		catch (\PDOException $e)
		{
			$result = $e->getMessage();
		}
		
		return $result;
	}
}

