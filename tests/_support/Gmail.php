<?php

use Helper\Utils;

class Gmail
{
    const APPLICATION_NAME = 'Gmail API Quickstart';
    const CREDENTIALS_PATH = 'gmail-token.json';
    const CLIENT_SECRET_PATH = 'client_secret.json';
    // If modifying these scopes, delete your previously saved credentials at /_data/gmail-token.json
    const SCOPES = Google_Service_Gmail::MAIL_GOOGLE_COM;

    private $service;
    private $message;
    private $user;

    public function __construct($user)
    {
        $client = $this->getClient();
        $this->service = new Google_Service_Gmail($client);
        $this->user = $user;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     * @throws Exception
     */
    public function getClient()
    {
        if (php_sapi_name() != 'cli') {
            throw new Exception('This application must be run on the command line.');
        }

        $client = new Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes(array(self::SCOPES));
        $client->setAuthConfig(codecept_data_dir(self::CLIENT_SECRET_PATH));
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = codecept_data_dir(self::CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    /**
     * @param $q
     */
    public function deleteOldMessages($q)
    {
        $messages = $this->listMessages($q);
        foreach ($messages as $mess) {
            $this->deleteMessage($mess['id']);
        }
    }

    /**
     * Get list of Messages in user's mailbox that uses search query.
     *
     * can be used to indicate the authenticated user.
     * @return array Array of Messages.
     */
    public function listMessages($q)
    {
        $pageToken = NULL;
        $messages = array();
        $opt_param = array('q' => $q);
        do {
            if ($pageToken) {
                $opt_param['pageToken'] = $pageToken;
            }

            $messagesResponse = $this->service->users_messages->listUsersMessages($this->user, $opt_param);
            if ($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();
            }
        } while ($pageToken);

        return $messages;
    }

    /**
     * Delete Message with given ID.
     *
     * @param string $messageId ID of Message to delete.
     */
    private function deleteMessage($messageId)
    {
        $this->service->users_messages->delete($this->user, $messageId);
    }

    /**
     * @param $q string
     * @param $waitTime int
     * @return mixed
     */
    public function getMessageBy($q, $waitTime = 60)
    {
        $time = 0;
        while ($time < $waitTime) {
            $messages = $this->listMessages($q);
            if (!empty($messages)) return $this->getMessage($messages[0]['id']);

            sleep(1);
            $time = $time + 1;
        }
    }

    /**
     * Get Message with given ID.
     *
     * @param string $messageId ID of Message to get.
     * @return Google_Service_Gmail_Message Message retrieved.
     */
    private function getMessage($messageId)
    {
        $this->message = $this->service->users_messages->get($this->user, $messageId);
        return $this->message;
    }

    /**
     * @return array
     */
    public function getAllLinks()
    {
        $dom = Utils::htmlToDOM($this->getMessageBody());
        $domNodeList = $dom->getElementsByTagName('a');

        $urls = array();
        foreach ($domNodeList as $node) {
            if ($node->attributes->length == 0) continue;
            array_push($urls, $node->attributes->getNamedItem('href')->value);
        }

        return $urls;
    }

    /**
     * @return bool|string
     */
    public function getMessageBody()
    {
        $payloadBody = $this->message['payload']['body'];
        if ($payloadBody['size'] > (102 * 1024) && array_key_exists('attachmentId', $payloadBody) && $payloadBody['attachmentId'] != null) {
            $body = $this->service->users_messages_attachments->get($this->user, $this->message['id'], $payloadBody['attachmentId'])['data'];
        } else {
            $body = $payloadBody['data'];
        }
        $data = str_replace(['-', '_'], ['+', '/'], $body);
        return base64_decode($data, true);
    }
}