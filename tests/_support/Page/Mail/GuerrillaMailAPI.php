<?php


namespace Page\Mail;


use ApiTester;

class GuerrillaMailAPI
{
    const URI = 'https://www.guerrillamail.com/ajax.php';
    const SET_EMAIL_USER_PATH = self::URI . '?f=set_email_user';
    const GET_EMAIL_LIST_PATH = self::URI . '?f=get_email_list&offset=0&site=guerrillamail.com&in=%s';
    const FETCH_EMAIL_PATH = self::URI . '?f=fetch_email&email_id=%s&site=guerrillamail.com&in=%s';

    private function filterEmailBySubject($subject)
    {
        $this->setGuerrillaEmail();

        // Wait for email sent
        $refreshTimes = 30;
        do {
            $emailList = $this->getEmailList();
            $emailListFilter = array_filter($emailList, function ($emailInfo) use ($subject) {
                if (strpos($emailInfo['mail_subject'], $subject) !== false) return $emailInfo;
            });
            $emailListFilter = array_values($emailListFilter); // reindex from 0

            if (count($emailListFilter) > 0) break;
            else {
                sleep(4);
                $refreshTimes -= 1;
            }
        } while ($refreshTimes > 0);

        if (count($emailListFilter) == 0) return null;

        return $this->getEmailContent($emailListFilter[0]['mail_id']);
    }

    private function setGuerrillaEmail()
    {
        $I = $this->apiTester;

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST(self::SET_EMAIL_USER_PATH, ['email_user' => $this->email, 'site' => 'guerrillamail.com']);
        $I->seeResponseCodeIs(200);
    }

    private function getEmailList()
    {
        $I = $this->apiTester;

        $I->sendGET(sprintf(self::GET_EMAIL_LIST_PATH, $this->email));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        return $I->grabDataFromResponseByJsonPath('$.list.*');
    }

    private function getEmailContent($emailId)
    {
        $I = $this->apiTester;

        $I->sendGET(sprintf(self::FETCH_EMAIL_PATH, $emailId, $this->email));
        $I->seeResponseCodeIs(200);
        return $I->grabDataFromResponseByJsonPath('$.')[0];
    }

    public function setEmail($email)
    {
        $this->email = explode('@', $email)[0];
    }

    private $email;
    /**
     * @var ApiTester
     */
    private $apiTester;

    public function __construct(ApiTester $apiTester)
    {
        $this->apiTester = $apiTester;
    }
}