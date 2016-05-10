<?php

App::uses('Controller', 'Controller');
App::uses('SwivelComponent', 'Swivel.Controller/Component');

class SwivelTestController extends Controller {
	public $components = array('Swivel.Swivel');
}

class SwivelComponentTest extends CakeTestCase {

	public $fixtures = ['plugin.swivel.swivel_feature'];

	public function setUp() {
		$this->Controller = new SwivelTestController(new CakeRequest(), new CakeResponse());
		$this->Controller->constructClasses();
		$this->Swivel = $this->Controller->Swivel;
		$this->Swivel->startup($this->Controller);
	}
	
	public function testWorking() {
		$this->assertSame('yes', $this->Swivel->invoke('FeatureAll', 'yes', 'no'));
		$this->assertSame('no', $this->Swivel->invoke('FeatureNone', 'yes', 'no'));
	}
}
