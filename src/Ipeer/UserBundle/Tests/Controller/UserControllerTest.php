<?php

namespace Ipeer\UserBundle\Tests\Controller;


use Liip\FunctionalTestBundle\Test\WebTestCase;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
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

    private $standardSampleDate = array('Ipeer\UserBundle\DataFixtures\ORM\LoadUserData');

    /**
     * @param string $method The HTTP method for the request
     * @param string $route The URL/route to access
     * @param string $body Body of request
     * @param int $statusCode Expected HTTP Status code
     * @param bool $decode Should the response be run through json_decode?
     * @return mixed|null|object
     */
    private function getJSONResponseFrom($method, $route, $body = '', $statusCode = 200, $decode = true) {
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

    public function setUp(){
        $this->client = static::createClient();
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200) {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    /*
     * =============================================
     * User Controller Tests
     * =============================================
     */

    public function testIndexActionEmpty() {
        $this->loadFixtures(array());
        $response = $this->getJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(0, $response["users"]);
    }

    public function testIndexAction() {
        $this->loadFixtures($this->standardSampleDate);
        $response = $this->getJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(3, $response["users"]);
    }

    public function testCreateAction() {
        $this->loadFixtures(array());

        $route =  $this->getUrl('user');

        $data = $this->getJSONResponseFrom("POST", $route,
            '{"first_name": "Test User", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $this->assertEquals(1, $data['id']);
        $this->assertEquals("Test User", $data['first_name']);
        $this->assertEquals("Action Test", $data['last_name']);
        $this->assertEquals("testcreateaction@ipeer.ubc", $data['email']);

        $data = $this->getJSONResponseFrom("POST", $route,
            '{"first_name": "Test2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $this->assertEquals(2, $data['id']);
        $this->assertEquals("Test2", $data['first_name']);
        $this->assertEquals("ActionTwo", $data['last_name']);
        $this->assertEquals("testcreateaction2@ipeer.ubc", $data['email']);

        $route =  $this->getUrl('user');
        $response = $this->getJSONResponseFrom("GET", $route);
        $this->assertCount(2, $response["users"]);
    }

    public function testShowAction() {
        $this->loadFixtures($this->standardSampleDate);

        for($i = 1; $i <= count(LoadUserData::$users); $i++) {
            $route =  $this->getUrl('user_show', array('id' => $i));
            $response = $this->getJSONResponseFrom("GET", $route);
            $data = $response['user'];
            $this->assertEquals(LoadUserData::$users[$i-1]->getFirstName(), $data['first_name']);
            $this->assertEquals(LoadUserData::$users[$i-1]->getLastName(), $data['last_name']);
            $this->assertEquals(LoadUserData::$users[$i-1]->getEmail(), $data['email']);
        }
    }

    public function testUpdateAction() {
        $this->loadFixtures($this->standardSampleDate);

        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update1", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getJSONResponseFrom('POST', $route,
            '{"id": 2, "first_name": "Update2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 3));
        $this->getJSONResponseFrom('POST', $route,
            '{"id": 3, "first_name": "Update3", "last_name": "ActionThree", "email": "testcreateaction3@ipeer.ubc"}');

        $route =  $this->getUrl('user');
        $response = $this->getJSONResponseFrom('GET', $route);
        $data = $response["users"];
        $this->assertCount(3, $data); //still 3 users; new ones should not have been created

        $this->assertEquals($data[0]["first_name"], "Update1");
        $this->assertEquals($data[1]["first_name"], "Update2");
        $this->assertEquals($data[2]["first_name"], "Update3");

    }

    public function testDeleteAction() {
        $this->loadFixtures($this->standardSampleDate);
/*
        $route =  $this->getUrl('user_delete', array('id' => 1));
        $this->client->request('DELETE', $route, array('ACCEPT' => 'application/json'));
        $route =  $this->getUrl('user_delete', array('id' => 2));
        $this->client->request('DELETE', $route, array('ACCEPT' => 'application/json'));
*/
    }

}
