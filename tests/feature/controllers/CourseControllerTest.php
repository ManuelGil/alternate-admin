<?php

use App\Controllers\CourseController;
use PHPUnit\Framework\TestCase;

/**
 * CourseControllerTest class
 *
 * @extends TestCase
 */
class CourseControllerTest extends TestCase
{

	// Define an instance of CourseController.
	private $courseController;

	/**
	 * This method creates the objects against which you will test.
	 */
	public function setUp(): void
	{
		// Inflate the instance.
		$this->courseController = new CourseController();
	}

	/**
	 * This method checks the user list when the course id is empty.
	 *
	 * @test
	 */
	public function testListUsersWithEmptyCourseID()
	{
		$this->assertEquals('[]', $this->courseController->getListUsers());
	}

	/**
	 * This method checks the user list when the course id is invalid.
	 *
	 * @test
	 */
	public function testListUsersWithInvalidCourseID()
	{
		$this->assertEquals('[]', $this->courseController->getListUsers('foo'));
	}
}
