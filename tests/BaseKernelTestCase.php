<?php


namespace Tests;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseKernelTestCase extends KernelTestCase {
	protected function tearDown()
	{
		parent::tearDown();

		\TestDataManager::restoreDatabase();
	}
}
