<?php

namespace Sendit;

use Sendit\Exception\Connection;

require_once (__DIR__ . '/../../config/config.php'); // Retrieve the $config variables


/**
 * Send It!
 *
 * @link      https://github.com/nicodiaz/sendit/ for the canonical source repository
 * @copyright Copyright (c) 2013 Nicolás Díaz País. (http://www.nicodp.com.ar)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * 
 * @author nicodiaz
 * @package Sendit
 * @version 0.01
 */
class Sendit
{

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
			$result = $this->sendMail($data['email'], $data['subject'], $data['body']);
			
			// If success, update the email as sent
			if ($result == true)
			{
				$conn->prepare('UPDATE emails SET sent = 1 WHERE id = :emailId')->execute(array(
					':emailId' => $data['id']
				));
			}
			
			// Next value
			$data = $stmt->fetch();
		}
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
		$mail->SMTPSecure = $GLOBALS['email_ssl']; // sets the prefix to the servier
		$mail->Host = $GLOBALS['email_host']; // sets the SMTP server
		$mail->Port = $GLOBALS['email_port']; // sets the SMTP port
		$mail->Username = $GLOBALS['email_username']; // email server username
		$mail->Password = $GLOBALS['email_password']; // email server password
			
		$mail->SetFrom($GLOBALS['email_from_email'], $GLOBALS['email_from_name']);
			
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
			
		$mail->AddAddress($to);
		
		//
		return (array_key_exists('mock_test_mail', $GLOBALS) && $GLOBALS['mock_test_mail'] == true)? true:$mail->Send();		
	}

	/**
	 * Add an email to the queue to be processed by the cron job
	 * Returns true on success or false on failure.
	 * 
	 * This functio doesnt work in transactional way, that must be implemented
	 * 
	 * @param string $email
	 * @param int $type
	 * 
	 * @return bool 
	 */
	public function queueEmail($email, $type = 1)
	{
		// Preconditions
		if (empty($email) || empty($type) || $type <= 0)
		{
			throw new \InvalidArgumentException("Error adding an email to the queue: is empty or the type doesn't exists");
		}
		
		$conn = $this->getConnection();
		
		$st = $conn->prepare('INSERT INTO emails (typeId, email) VALUES(:typeId, :email)');
		
		return $st->execute(array(
			':typeId' => $type, 
			':email' => $email
		));
	}

	/**
	 * Function to create the (initial) connection to MySQL Database
	 *
	 * @return void
	 */
	protected function initConnection()
	{
		$dsn = 'mysql:host=' . $GLOBALS['config']['host'] . ';port=' . $GLOBALS['config']['port'] . ';dbname=' .
		 $GLOBALS['config']['dbname'];
		$username = $GLOBALS['config']['user'];
		$password = $GLOBALS['config']['password'];
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

