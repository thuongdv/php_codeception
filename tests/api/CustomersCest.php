<?php 

use Helper\Api\Customers;

class CustomersCest
{
    public function tc01GetCustomer(ApiTester $I, Customers $customers)
    {
        $I->sendGET(sprintf(Customers::PATH, ACCOUNT_DATA['customerId']));
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsXml();
    }
}
