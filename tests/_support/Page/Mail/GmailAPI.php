<?php


namespace Page\Mail;


use Codeception\Exception\Fail;
use Gmail;
use Helper\Utils;
use UITester;

class GmailAPI
{
    const TEST_EMAIL_QUERY = 'from:support@test.com subject:(SUBJECT WILL BE HEER)';

    const PASSWORD_LBL = './/*[contains(text(), "temporary password")]/b';

    public function verifyTestEmail($accountId)
    {
        $I = $this->uiTester;

        $emailContent = file_get_contents(codecept_data_dir('emails/test.html'));
        $emailContent = sprintf($emailContent, $I->getCurrentConfigUrl('REST'), $accountId, $I->getCurrentConfigUrl('WebDriver') . '/ui');
        $this->checkEmail($emailContent, self::TEST_EMAIL_QUERY);
    }

    public function getTemporaryPassword($from)
    {
        $this->isReceivedEmail($from);
        $body = $this->gMailApi->getMessageBody();
        $dom = Utils::htmlToDOMXpath($body);
        return $dom->query(self::PASSWORD_LBL)->item(0)->nodeValue;
    }

    private function checkEmail($emailContent, $emailQuery)
    {
        $I = $this->uiTester;
        $this->isReceivedEmail($emailQuery);
        $body = $this->gMailApi->getMessageBody();
        $I->verifyEquals(Utils::stripData($emailContent), Utils::stripData($body));
    }

    private function isReceivedEmail($emailQuery)
    {
        $I = $this->uiTester;

        $message = $this->gMailApi->getMessageBy($emailQuery, 5 * 60);
        if (empty($message)) {
            $I->comment('There is no email');
            throw new Fail();
        }
    }

    protected $uiTester;
    /**
     * @var Gmail
     */
    private $gMailApi;

    public function __construct(UITester $I)
    {
        $this->uiTester = $I;
        $this->gMailApi = new Gmail('me');
    }
}