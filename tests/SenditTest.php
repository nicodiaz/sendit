<?php

use Sendit\Sendit;


/**
 * Sendit test case.
 */
class SenditTest extends \PHPUnit_Extensions_Database_TestCase
{
	// only instantiate pdo once for test clean-up/fixture load
	private static $pdo = null;
	
	// only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
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
		$this->Sendit->queueEmail(/* parameters */);
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

