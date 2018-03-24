<?php

namespace Socialite\Provider;

use Socialite\Two\AbstractProvider;
use Socialite\Two\User;
use Socialite\Util\A;

class YandexProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(string $state)
    {
        return $this->buildAuthUrlFromBase(
            'https://oauth.yandex.ru/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://oauth.yandex.ru/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(string $token)
    {
        $response = $this->getHttpClient()->get(
            'https://login.yandex.ru/info?format=json', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['login'],
            'name' => A::get($user, 'real_name'),
            'email' => A::get($user, 'default_email'),
            'avatar' => 'https://avatars.yandex.net/get-yapic/' . A::get($user, 'default_avatar_id') . '/islands-200',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields(string $code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
