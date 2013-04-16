<?php

namespace Sendit;

use Sendit\Exception\Connection;

require_once(__DIR__ . '/../../config/config.php'); // Retrieve the $config variables

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
	 * array
	 */
	
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
	 * TODO
	 */
	public function processQueue()
	{
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
		
		return $st->execute( array( ':typeId' => $type, ':email' => $email ) );		
	}

	/**
	 * Function to create the (initial) connection to MySQL Database
	 *
	 * @return void
	 */
	protected function initConnection()
	{
		$dsn = 'mysql:host=' . $GLOBALS['config']['host'] . ';port=' . $GLOBALS['config']['port'] . ';dbname=' . $GLOBALS['config']['dbname'];
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

