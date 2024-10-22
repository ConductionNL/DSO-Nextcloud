<?php

namespace OCA\ZaakAfhandelApp\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

/**
 * Class Application
 *
 * @package OCA\ZaakAfhandelApp\AppInfo
 */
class Application extends App implements IBootstrap
{
	public const APP_ID = 'zaakafhandelapp';

	/**
	 * Constructor
	 *
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = [])
	{
		parent::__construct(appName: self::APP_ID, urlParams: $urlParams);
	}

	public function register(IRegistrationContext $context): void
	{
	}

	public function boot(IBootContext $context): void
	{
	}
}
