<?php

use App\Controllers\EnrollmentController;
use PHPUnit\Framework\TestCase;

/**
 * EnrollmentControllerTest class
 *
 * @extends TestCase
 */
class EnrollmentControllerTest extends TestCase
{

	// Define an instance of EnrollmentController.
	private $enrollmentController;

	/**
	 * This method creates the objects against which you will test.
	 */
	public function setUp(): void
	{
		// Inflate the instance.
		$this->enrollmentController = new EnrollmentController();
	}

	/**
	 * This method checks the role assignment list when the course id is empty.
	 *
	 * @test
	 */
	public function testListModuleWithEmptyenrollmentID()
	{
		$this->assertEquals('[]', $this->enrollmentController->getListAssignments());
	}

	/**
	 * This method checks the role assignment list when the course id is empty.
	 *
	 * @test
	 */
	public function testListModuleWithInvalidenrollmentID()
	{
		$this->assertEquals('[]', $this->enrollmentController->getListAssignments('foo'));
	}
}
