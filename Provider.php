<?php

namespace SocialiteProviders\Discogs;

use InvalidArgumentException;
use SocialiteProviders\Manager\OAuth1\AbstractProvider;
use SocialiteProviders\Manager\OAuth1\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'DISCOGS';

    /**
     * {@inheritdoc}
     */
    protected function getHttpClient()
    {
        return new \GuzzleHttp\Client([
            'headers' => [
                'User-Agent' => $this->config['user_agent'] ?? 'socialite-discogs',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if (! $this->hasNecessaryVerifier()) {
            throw new InvalidArgumentException('Invalid request. Missing OAuth verifier.');
        }
        $token = $this->getToken();
        $tokenCredentials = $token['tokenCredentials'];
        $user = $this->server->getUserDetails($tokenCredentials);

        return (new User)->setRaw($user->extra)->map([
            'id'       => $user->id,
            'nickname' => $user->nickname,
            'name'     => $user->name,
            'email'    => $user->email,
            'avatar'   => $user->avatar,
        ])->setToken($tokenCredentials->getIdentifier(), $tokenCredentials->getSecret());
    }
}
