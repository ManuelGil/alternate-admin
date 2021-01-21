<?php

use App\Controllers\UserController;
use PHPUnit\Framework\TestCase;

/**
 * UserControllerTest class
 *
 * @extends TestCase
 */
class UserControllerTest extends TestCase
{

	// Define an instance of UserController.
	private $userController;

	/**
	 * This method creates the objects against which you will test.
	 */
	public function setUp(): void
	{
		// Inflate the instance.
		$this->userController = new UserController();
	}

	/**
	 * This method checks the course list when the user id is empty.
	 *
	 * @test
	 */
	public function testListCoursesWithEmptyUserID()
	{
		$this->assertEquals('[]', $this->userController->getListCourses());
	}


	/**
	 * This method checks the course list when the user id is invalid.
	 *
	 * @test
	 */
	public function testListCoursesWithInvalidUserID()
	{
		$this->assertEquals('[]', $this->userController->getListCourses('foo'));
	}
}
