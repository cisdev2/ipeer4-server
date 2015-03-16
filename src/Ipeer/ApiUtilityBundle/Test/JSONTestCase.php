<?php

namespace Ipeer\ApiUtilityBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;

class JSONTestCase extends WebTestCase
{

    /*
    * =============================================
    * Helper Methods
    * =============================================
    */

    /**
     * @var Client
     * */
    private $client;

    /**
     * @param string $method The HTTP method for the request
     * @param string $route The URL/route to access
     * @param string $body Body of request
     * @param int $statusCode Expected HTTP Status code
     * @param bool $decode Should the response be run through json_decode?
     * @return mixed|null|object
     */
    protected function getAndTestJSONResponseFrom($method, $route, $body = '', $statusCode = 200, $decode = true)
    {
        $this->client->request(
            $method,
            $route,
            array('Accept' => 'application/json'),
            array(),
            array("CONTENT_TYPE" => "application/json"), // since the server only accepts json for deserialization
            $body
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, $statusCode);
        return $decode ? json_decode($response->getContent(), true) : $response;
    }

    public function setUp()
    {
        $this->client = static::createClient();
    }

    private function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $content = $response->getContent();
        if(!empty($content)) {
            $this->assertEquals(
                $statusCode, $response->getStatusCode()
            );
            $this->assertTrue(
                $response->headers->contains('Content-Type', 'application/json'),
                "Did not get the expected application/json Content-Type."
            );
        }
    }
}
