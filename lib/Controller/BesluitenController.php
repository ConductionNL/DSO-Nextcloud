<?php

namespace OCA\ZaakAfhandelApp\Controller;

use GuzzleHttp\Client;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;


/**
 * Geeft invulling aan https://vng-realisatie.github.io/gemma-zaken/standaard/zaken/
 */
class BesluitenController extends Controller
{
    const TEST_ARRAY = [
        "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f" => [
            "id" => "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f",
            "name" => "Github",
            "summary" => "summary for one"
        ],
        "4c3edd34-a90d-4d2a-8894-adb5836ecde8" => [
            "id" => "4c3edd34-a90d-4d2a-8894-adb5836ecde8",
            "name" => "Gitlab",
            "summary" => "summary for two"
        ],
        "15551d6f-44e3-43f3-a9d2-59e583c91eb0" => [
            "id" => "15551d6f-44e3-43f3-a9d2-59e583c91eb0",
            "name" => "Woo",
            "summary" => "summary for two"
        ],
        "0a3a0ffb-dc03-4aae-b207-0ed1502e60da" => [
            "id" => "0a3a0ffb-dc03-4aae-b207-0ed1502e60da",
            "name" => "Decat",
            "summary" => "summary for two"
        ]
    ];

    public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config
	)
    {
        parent::__construct($appName, $request);
    }

	/**
	 * This returns the template of the main app's page
	 * It adds some data to the template (app version)
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function page(): TemplateResponse
	{			
        return new TemplateResponse(
            //Application::APP_ID,
            'zaakafhandelapp',
            'index',
            []
        );
	}
	

    /**
     * Return (and serach) all objects
     * 
     * @NoAdminRequired
     * @NoCSRFRequired
	 *
	 * @return JSONResponse
     */
    public function index(): JSONResponse
    {
        $results = ["results" => self::TEST_ARRAY];
        return new JSONResponse($results);
    }

    /**
     * Read a single object
     * 
     * @NoAdminRequired
     * @NoCSRFRequired
	 *
	 * @return JSONResponse
     */
    public function show(string $id): JSONResponse
    {
        $result = self::TEST_ARRAY[$id];
        return new JSONResponse($result);
    }


    /**
     * Creatue an object
     * 
     * @NoAdminRequired
     * @NoCSRFRequired
	 *
	 * @return JSONResponse
     */
    public function create(): JSONResponse
    {
        // get post from requests
        return new JSONResponse([]);
    }

    /**
     * Update an object
     * 
     * @NoAdminRequired
     * @NoCSRFRequired
	 *
	 * @return JSONResponse
     */
    public function update(string $id): JSONResponse
    {
        $result = self::TEST_ARRAY[$id];
        return new JSONResponse($result);
    }

    /**
     * Delate an object
     * 
     * @NoAdminRequired
     * @NoCSRFRequired
	 *
	 * @return JSONResponse
     */
    public function destroy(string $id): JSONResponse
    {
        return new JSONResponse([]);
    }
}