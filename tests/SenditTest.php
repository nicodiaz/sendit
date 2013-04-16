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
		
		// TODO Auto-generated SenditTest::setUp()
		

		$this->Sendit = new Sendit();
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
		$this->assertTrue($this->Sendit->queueEmail('test@example.com'));
		$this->assertTrue($this->Sendit->queueEmail('test@example.com', 2));
		$this->assertTrue($this->Sendit->queueEmail('test@example.com', 5));
		$this->assertFalse($this->Sendit->queueEmail('test@example.com', 99));
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

