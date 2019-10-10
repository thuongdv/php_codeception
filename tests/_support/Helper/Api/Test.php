<?php

namespace Helper\Api;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use ApiTester;
use Codeception\Module;

class Test extends Module
{
    const PATH = '/path';

    public function verifyTopUser()
    {
        $I = $this->apiTester;

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    protected $apiTester;

    public function __construct(ApiTester $I)
    {
        $this->apiTester = $I;
    }
}
