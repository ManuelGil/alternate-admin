<?php

use App\Controllers\ActivityController;
use PHPUnit\Framework\TestCase;

/**
 * ActivityControllerTest class
 *
 * @extends TestCase
 */
class ActivityControllerTest extends TestCase
{

	// Define an instance of ActivityController.
	private $activityController;

	/**
	 * This method creates the objects against which you will test.
	 */
	public function setUp(): void
	{
		// Inflate the instance.
		$this->activityController = new ActivityController();
	}

	/**
	 * This method checks the activities list when the course id is empty.
	 *
	 * @test
	 */
	public function testListModuleWithEmptyActivityID()
	{
		$this->assertEquals('[]', $this->activityController->getListModule('forum'));
	}

	/**
	 * This method checks the activities list when the course id is empty.
	 *
	 * @test
	 */
	public function testListModuleWithInvalidActivityID()
	{
		$this->assertEquals('[]', $this->activityController->getListModule('forum', 'foo'));
	}
}
