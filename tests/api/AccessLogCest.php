<?php

use Helper\Api\Test;
use Helper\Api\Account;
use Helper\Api\Api;
use Helper\Api\Session;

class AccessLogCest
{
    private $sessionInfo;

    /**
     * @group data
     * @before login
     */
    public function tc01GetAccessLogTimeline(ApiTester $I)
    {
        $I->sendGET(sprintf(Test::PATH_ACCESS_LOG_TIMELINE, $this->sessionInfo['accountId']));
        $I->seeResponseCodeIs(200);
    }

    /**
     * @group data
     * @before login
     */
    public function tc03GetAccessLogs(ApiTester $I)
    {
        $I->sendGET(sprintf(Test::PATH_ACCESS_LOG, $this->sessionInfo['accountId'], Api::todayString(), $this->sessionInfo['userId']));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'Data' => 'array',
            'Total' => 'integer',
            'Timezone' => 'string'
        ]);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'userid' => 'integer',
            'timestamp' => 'string',
            'type' => 'string',
            'details' => 'string',
            'email' => 'string',
            'end_date' => 'integer',
            'useragent' => 'string',
            'ip' => 'string'
        ], '$..Data.*');
    }

    /**
     * @group data
     * @before login
     */
    public function tc04FilterByEmail(ApiTester $I)
    {
        $I->sendGET(sprintf(Test::PATH_FILTER_BY_EMAIL, $this->sessionInfo['accountId'], $this->sessionInfo['userId'], $this->sessionInfo['email']));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'Data' => [[
                'userid' => $this->sessionInfo['userId'],
                'email' => $this->sessionInfo['email']
            ]]
        ]);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'userid' => 'integer',
            'timestamp' => 'string',
            'type' => 'string',
            'details' => 'string',
            'email' => 'string',
            'end_date' => 'integer'
        ], '$..Data.*');
    }

    /**
     * @group data
     * @before login
     */
    public function tc05GetAccessLogTopUsers(ApiTester $I, Test $accessLog)
    {
        $I->sendGET(sprintf(Test::PATH_ACCESS_LOG_TOPUSERS, $this->sessionInfo['accountId'], $this->sessionInfo['userId']));
        $accessLog->verifyTopUser();
    }

    protected function login(ApiTester $I, Session $session)
    {
        if ($this->sessionInfo != null) {
            $I->haveHttpHeader(Api::HEADER_ACCESS_TOKEN, $this->sessionInfo['accessToken']);
            return;
        }


        $sessionInfo = $session->getSessionResponseInfo(
            Account::ACCOUNT_INFO_READ_ONLY['email'],
            Account::ACCOUNT_INFO_READ_ONLY['password'],
            Account::ACCOUNT_INFO_READ_ONLY['orgName']
        );
        $this->sessionInfo = $sessionInfo;
    }
}