<?php
namespace Helper\Api;

class Customers
{
    const PATH = '/customers/%s?_type=json';

    public function verifyCustomerInfo()
    {
        $I = $this->apiTester;

        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $expected = ACCOUNT_DATA;
        unset($expected['password'], $expected['username'], $expected['id']);
        $I->seeValueEquals($expected, json_decode($I->grabResponse(), true));
    }

    protected $apiTester;
    public function __construct(\ApiTester $I)
    {
        $this->apiTester = $I;
    }
}
