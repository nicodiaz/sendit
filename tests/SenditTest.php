<?php

use Sendit\Sendit;


/**
 * Sendit test case.
 */
class SenditTest extends \PHPUnit_Extensions_Database_TestCase
{
	// only instantiate pdo once for test clean-up/fixture load
	private static $pdo = null;

	/**
	 * only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
	 * @var PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	private $conn = null;

	/**
	 * @var Sendit
	 */
	private $Sendit;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$config = array(
			'host' => 'localhost', // dummy value, the test use $GLOBALS['DB_DSN'] value
			'port' => '3325', // dummy value, the test use $GLOBALS['DB_DSN'] value
			'user' => $GLOBALS['DB_USER'],
			'password' => $GLOBALS['DB_PASSWD'],
			'dbname' => $GLOBALS['DB_DBNAME'],
			'email_host' => $GLOBALS['email_host'],
			'email_ssl' => $GLOBALS['email_ssl'],
			'email_port' => $GLOBALS['email_port'],
			'email_username' => $GLOBALS['email_username'],
			'email_password' => $GLOBALS['email_password'],
			'email_from_email' => $GLOBALS['email_from_email'],
			'email_from_name' => $GLOBALS['email_from_name'],
			'mock_test_mail' => ! $GLOBALS['send_test_mail'],
		);

		$this->Sendit = new Sendit($config);
		$this->Sendit->setConnection($this->getConnection()->getConnection());
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		// TODO Auto-generated SenditTest::tearDown()
		$this->Sendit = null;
		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}

	public function testTypesTable()
	{
		$dataSet = $this->getConnection()->createDataSet(array(
			'types'
		));
		$this->assertNotEmpty($dataSet);
	}

	/**
	 * Tests Sendit->queueEmail()
	 */
	public function testQueueEmail()
	{
		$this->assertTrue($this->Sendit->queueEmail($GLOBALS['TEST_EMAIL']));
		$this->assertTrue($this->Sendit->queueEmail($GLOBALS['TEST_EMAIL'], 2));
		$this->assertTrue($this->Sendit->queueEmail($GLOBALS['TEST_EMAIL'], 5));
		$this->assertFalse($this->Sendit->queueEmail($GLOBALS['TEST_EMAIL'], 99));
	}

	public function testProcessQueue()
	{
		$this->Sendit->queueEmail($GLOBALS['TEST_EMAIL']);
		$this->Sendit->queueEmail($GLOBALS['TEST_EMAIL'], 5);
		
		$GLOBALS['mock_test_mail'] = true;
		
		$this->Sendit->processQueue();
	}

	public function testSendMails()
	{
		if ($GLOBALS['send_test_mail'])
		{
			$mail = new PHPMailer();
			
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->SMTPSecure = $GLOBALS['email_ssl']; // sets the prefix to the servier
			$mail->Host = $GLOBALS['email_host']; // sets GMAIL as the SMTP server
			$mail->Port = $GLOBALS['email_port']; // set the SMTP port for the GMAIL server
			$mail->Username = $GLOBALS['email_username']; // GMAIL username
			$mail->Password = $GLOBALS['email_password']; // GMAIL password
			
	
			$mail->SetFrom($GLOBALS['email_from_email'], $GLOBALS['email_from_name']);
			
			$mail->Subject = "Send It With PHPMailer Test Subject via smtp";
			
			$body = "<h1>This is a test Mail</h1><h2>With HTML Format</h2><p>it's ok, isn't it?</p>";
	
			$mail->MsgHTML($body);
			
			$address = $GLOBALS['TEST_EMAIL'];
			$mail->AddAddress($address);
	
			$this->assertTrue($mail->Send());
		}
	}
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Extensions_Database_TestCase::getConnection()
	 */
	protected function getConnection()
	{
		if ($this->conn === null)
		{
			if (self::$pdo == null)
			{
				self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
			}
			$this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
			
			$this->conn->getConnection()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
		}
		
		return $this->conn;
	}
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	protected function getDataSet()
	{
		return $this->createMySQLXMLDataSet('dataset.xml');
	}
}

