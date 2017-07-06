<?php


	namespace LiftKit\Tests\Unit\EnvironmentDetection;

	use PHPUnit\Framework\TestCase;
	use LiftKit\EnvironmentDetection\Detector;


	class DetectorTest extends TestCase
	{

		/**
		 * @var Detector
		 */
		protected $detector;


		public function setUp ()
		{
			$this->detector = new Detector;
		}


		public function testEmpty ()
		{
			$this->assertNull($this->detector->resolve());
		}


		public function testMatch ()
		{
			$this->assertTrue($this->detector->match('test.local', '*'));
			$this->assertTrue($this->detector->match('test.local', 'test.local'));
			$this->assertTrue($this->detector->match('test.local', '*.local'));
			$this->assertTrue($this->detector->match('test.local', '*local'));
			$this->assertTrue($this->detector->match('test.local', '*test.local*'));
			$this->assertTrue($this->detector->match('1test.local2', '*test.local*'));

			$this->assertFalse($this->detector->match('asd', 'test.local'));
			$this->assertFalse($this->detector->match('stest.local1', '*local'));
			$this->assertFalse($this->detector->match('test.local', 'est.local*'));
		}


		public function testIfHttpHost ()
		{
			$_SERVER['HTTP_HOST'] = 'test.localhost';

			$this->detector->ifHttpHost('*.localhost', 'test1');

			$this->assertEquals(
				'test1',
				$this->detector->resolve()
			);
		}


		public function testIfHostName ()
		{
			$hostname = php_uname('n');

			$this->detector->ifHostName($hostname, 'test2');

			$this->assertEquals(
				'test2',
				$this->detector->resolve()
			);
		}


		public function testIfEnv ()
		{
			$_ENV['test'] = 'test.localhost';

			$this->detector->ifEnv('test', '*.localhost', 'test3');

			$this->assertEquals(
				'test3',
				$this->detector->resolve()
			);
		}


		public function testIfMatch ()
		{
			$this->detector->ifMatch('test.localhost', '*.localhost', 'test4');

			$this->assertEquals(
				'test4',
				$this->detector->resolve()
			);
		}


		public function testIfBoolTrue ()
		{
			$this->detector->ifBool(true, 'test5');

			$this->assertEquals(
				'test5',
				$this->detector->resolve()
			);
		}


		public function testIfBoolFalse ()
		{
			$this->detector->ifBool(false, 'test6');

			$this->assertNull(
				$this->detector->resolve()
			);
		}


		public function testClear ()
		{
			$this->detector->ifBool(true, 'test7');
			$this->detector->clear();

			$this->assertNull(
				$this->detector->resolve()
			);
		}


		public function testMultiple ()
		{
			$this->detector->ifMatch('asdadas', 'nomatch', 'match')
				->ifHttpHost('definitelynotgonnamatch.local', 'http-host')
				->ifBool(true, 'bool')
				->ifHostName('definitelynotgonnamatch.local', 'host-name');

			$this->assertEquals(
				'bool',
				$this->detector->resolve()
			);
		}
	}