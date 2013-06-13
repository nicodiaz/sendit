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
	 * Tests Sendit->enqueueEmail()
	 */
	public function testEnqueueEmail()
	{
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL']));
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 2));
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 5));
		$this->assertFalse($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 99));
		
		// Now with params
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 5, array('param1')));
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 5, array('param1', 23)));
		$this->assertTrue($this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 5, array('param1', 23, '123', array('innerParam'))));
	}

	public function testProcessQueue()
	{
// 		$this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL']);
// 		$this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 5);
		
		// With Params
		$this->Sendit->enqueueEmail($GLOBALS['TEST_EMAIL'], 3, array('
			http://www.example.com/confirm?id=33213 
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent bibendum eros ornare, lacinia eros sit amet, fermentum purus. Vestibulum hendrerit et tellus vel egestas. Proin euismod venenatis odio, at rhoncus mauris aliquam quis. Cras et risus ligula. Suspendisse venenatis fringilla nisi sed tempus. Donec bibendum semper felis, eu posuere augue ultrices non. Praesent consequat purus lectus, eget fermentum mi eleifend sed. Sed a mauris non enim luctus porttitor.

Proin felis justo, tristique vitae metus at, hendrerit sodales lacus. Duis interdum, quam ac dignissim porta, mauris neque viverra erat, a tincidunt neque massa eget nisi. Quisque sodales mattis auctor. Sed ac ultricies ante. Maecenas id semper erat. Praesent et est fringilla, venenatis tellus vel, vehicula urna. Nullam ante lectus, aliquet ut scelerisque suscipit, vehicula ut diam. Proin ac risus dui. Aenean ipsum nisi, ullamcorper luctus libero in, euismod ultrices augue. Donec at felis urna. Mauris rhoncus purus vitae viverra pharetra. Maecenas venenatis tincidunt velit at tristique.

Morbi arcu felis, gravida faucibus auctor sed, tempor eu tortor. Integer lacinia interdum enim, at lobortis magna. Etiam lacinia purus eget libero sodales, vitae tempor sem elementum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce eget congue purus. Pellentesque quis condimentum velit. Nam suscipit porta augue id hendrerit. Fusce nec dictum lacus. Suspendisse vulputate diam at ante blandit semper. Maecenas iaculis consequat quam, non porttitor libero mattis quis. Donec sit amet tristique magna. Maecenas ullamcorper, tortor id luctus ullamcorper, massa orci rutrum tortor, ut sodales sapien augue non est.

Fusce rhoncus vestibulum orci, id posuere nisi dapibus sed. Nunc semper, neque sollicitudin lacinia eleifend, dolor felis pulvinar erat, a accumsan metus nisi id nisi. Vivamus sed felis et odio lacinia cursus. Nam viverra, augue rhoncus volutpat elementum, nulla tellus mollis magna, faucibus tempor turpis leo sed ante. Vivamus tincidunt nulla eu rutrum ornare. Donec interdum justo at quam sollicitudin suscipit. Ut ullamcorper aliquet feugiat. Quisque non pellentesque justo. Maecenas sodales dictum turpis eget blandit. Morbi hendrerit nunc eget lorem fermentum mollis. Suspendisse mattis pellentesque augue tincidunt auctor. Quisque facilisis lacus in tortor sagittis, at molestie arcu volutpat. Suspendisse ultrices magna in pulvinar posuere. Proin quis viverra augue, eu ullamcorper odio. Cras sed lacus pretium, pharetra velit nec, lacinia mi. Curabitur dapibus, odio id placerat condimentum, quam tortor condimentum elit, vitae egestas nunc ante a est.

Etiam bibendum tellus nec lacus convallis consectetur. Nam eu risus laoreet, aliquet nunc vitae, aliquam odio. Sed id tellus ante. Curabitur lacus dui, fermentum nec sapien eget, vulputate congue ante. Ut velit enim, auctor vitae mi sed, venenatis tincidunt leo. Nam vel tellus tortor. Etiam ullamcorper porta luctus. Nam ac interdum est. Phasellus elementum tellus urna, a pellentesque enim congue non. Pellentesque a feugiat eros.

Suspendisse lacus odio, vehicula sed bibendum vitae, ultrices vitae ipsum. Aliquam placerat, erat congue gravida faucibus, eros velit ultrices libero, quis suscipit erat mauris vel libero. Nullam sed justo arcu. Phasellus mollis laoreet massa, ac condimentum metus cursus id. Aenean cursus eu velit vel pulvinar. Etiam orci orci, gravida sit amet mauris quis, scelerisque tincidunt ipsum. Vestibulum fermentum enim nisi, at dictum velit sagittis pretium. Proin ornare justo nunc, vitae ultricies purus tristique id. Donec mattis consectetur nisl at blandit. Vivamus aliquet dui sed condimentum eleifend. Praesent at sagittis mauris. Nullam porta urna dolor, quis facilisis massa consectetur ut. Fusce velit sem, dictum id viverra imperdiet, congue eget ligula. Etiam iaculis varius est, vitae ultricies orci interdum ut.

Quisque sit amet ultricies mauris. Nunc hendrerit nisl sit amet nibh congue tristique. Aenean tincidunt, enim id iaculis blandit, magna mi faucibus tortor, eget aliquet tellus nibh porttitor nibh. Suspendisse faucibus fermentum porta. Vestibulum lobortis tellus non massa cursus tempor. Sed blandit pulvinar elit, sed rutrum risus mattis a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas at risus imperdiet, porttitor diam a, consequat nisl. Aenean blandit, lacus ut malesuada porta, elit purus faucibus sem, et pellentesque risus purus id lacus. Maecenas tempus odio cursus lacus pretium, sit amet rutrum magna congue. Ut sollicitudin condimentum hendrerit.

Praesent ultrices ultricies risus vel adipiscing. Maecenas molestie eu nunc nec rhoncus. Sed rutrum risus sed sagittis hendrerit. Fusce eu mauris eget arcu feugiat auctor. Nunc nec felis condimentum, tincidunt arcu a, imperdiet erat. Nunc sodales odio id ipsum congue, tincidunt semper lectus pharetra. Nulla eleifend vestibulum tellus, vel dictum augue pellentesque nec. Vivamus volutpat, risus vitae egestas consectetur, ipsum erat dictum ligula, vel rhoncus elit magna eu diam. Sed iaculis nec turpis ut scelerisque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;

Aenean sit amet sagittis nisl. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus porta, tortor nec cursus feugiat, lacus arcu lobortis metus, sed blandit augue nulla vitae est. Donec diam risus, varius nec justo nec, pulvinar tempor tellus. Maecenas eu elit mauris. Praesent tempor, elit eu mollis hendrerit, est nulla egestas metus, at iaculis ipsum risus quis dui. Morbi in eros ante. Pellentesque tincidunt, nisi quis mattis pulvinar, nisl lorem ornare nibh, sit amet lacinia risus enim sit amet nisl. Donec vulputate bibendum mauris, ut vehicula ligula sagittis id. Praesent vestibulum diam porta, consectetur erat eget, semper tellus. Aliquam sed nisi laoreet, pulvinar sapien a, vehicula diam. Phasellus dictum commodo aliquet. Donec lacinia scelerisque urna, in interdum elit commodo eget. Praesent iaculis bibendum neque, eu ornare justo ultrices eget. Curabitur ipsum sem, pellentesque vel quam ultrices, porttitor malesuada leo.

Nullam consequat lorem in commodo mattis. Mauris blandit molestie purus. Nulla accumsan, dui eu elementum lobortis, justo mi mattis neque, eget sagittis arcu lorem ac sem. Maecenas ultricies libero ut odio tempus facilisis. Suspendisse neque risus, ultrices id sapien nec, iaculis tristique ante. Praesent auctor egestas rhoncus. Ut condimentum mi a est facilisis, sit amet facilisis quam egestas. Quisque dolor ipsum, scelerisque vitae commodo a, egestas sit amet velit. Morbi ut hendrerit elit. Aliquam quis mattis tellus. Sed ac ligula id dolor placerat bibendum. Quisque eu blandit mauris, malesuada molestie metus. Cras ipsum urna, sollicitudin at eros nec, faucibus condimentum diam. Nunc euismod massa et laoreet auctor.

		</p>
		', 10021.25));
		
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

