<?php

namespace Light;

class Fcm
{
    /**
     * @var string
     */
    private $url = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var string
     */
    private $apiKey = null;

    /**
     * @var array
     */
    private $tokens = [];

    /**
     * @var string
     */
    private $title = null;

    /**
     * @var string
     */
    private $body = null;

    /**
     * @var string
     */
    private $icon = null;

    /**
     * @var string
     */
    private $clickAction = null;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Fcm
     */
    public function setUrl(string $url): Fcm
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return Fcm
     */
    public function setApiKey(string $apiKey): Fcm
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param array $tokens
     * @return Fcm
     */
    public function setTokens(array $tokens): Fcm
    {
        $this->tokens = $tokens;
        return $this;
    }

    /**
     * @param string $token
     * @return Fcm
     */
    public function addToken(string $token): Fcm
    {
        $this->tokens[] = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Fcm
     */
    public function setTitle(string $title): Fcm
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Fcm
     */
    public function setBody(string $body): Fcm
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return Fcm
     */
    public function setIcon(string $icon): Fcm
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getClickAction(): string
    {
        return $this->clickAction;
    }

    /**
     * @param string $clickAction
     * @return Fcm
     */
    public function setClickAction(string $clickAction): Fcm
    {
        $this->clickAction = $clickAction;
        return $this;
    }

    /**
     * Fcm constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $value) {

            if (is_callable([$this, 'set' . ucfirst($name)])) {
                call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
            }
        }

        $apiKey = Front::getInstance()->getConfig()['light']['fcm']['key'] ?? false;

        if (!$this->apiKey && $apiKey) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * @return array
     */
    public function send()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: key=' . $this->getApiKey(),
        ]);

        $notificaton = [];

        if ($icon = $this->getIcon()) {
            $notificaton['icon'] = $icon;
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'registration_ids' => $this->getTokens(),
            'notification' => array_merge($notificaton, [
                'title' => $this->getTitle(),
                'body' => $this->getBody(),
                'click_action' => $this->getClickAction(),
            ])
        ]));

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }
}
