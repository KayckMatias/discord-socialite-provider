<?php

declare(strict_types=1);

namespace KayckMatias\Laravel\Socialite;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

/**
 * Discord OAuth2 provider.
 *
 * @author Kayck Matias <kayckmatias@gmail.com>
 *
 * @see https://discord.com/developers/docs/topics/oauth2
 */
class DiscordProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base URL to use for the authentication request.
     */
    private const DISCORD_AUTH_URL = 'https://discord.com/api/oauth2/authorize';

    /**
     * The base URL to use for the token request.
     */
    private const DISCORD_TOKEN_URL = 'https://discord.com/api/oauth2/token';

    /**
     * The base URL to use for the user request.
     */
    private const DISCORD_USER_URL = 'https://discord.com/api/users/@me';

    /**
     * The base URL to use for user avatar.
     */
    private const DISCORD_AVATAR_URL = 'https://cdn.discordapp.com/avatars/';

    /**
     * The default scopes.
     *
     * @var array
     */
    protected $scopes = ['identify', 'email'];

    /**
     * The permissions to be added to the authorization URL.
     *
     * @var string|null
     */
    protected $permissions = null;

    /**
     * Whether to add the "prompt=none" parameter to the authorization URL.
     *
     * @var bool
     */
    protected $consent = false;

    /**
     * The scope separator used in the authorization URL.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Set the "prompt=none" parameter to the authorization URL.
     *
     * @return $this
     */
    public function withConsent(): self
    {
        $this->consent = true;
        return $this;
    }

    /**
     * Set the permissions to be added to the authorization URL.
     *
     * @param string $permissions
     * @return $this
     */
    public function withPermissions(string $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Set the scope to be added to the authorization URL as a bot.
     *
     * @return $this
     */
    public function asBot(): self
    {
        $this->scopes = ['bot'];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        $authUrl = $this->buildAuthUrlFromBase(
            url: self::DISCORD_AUTH_URL,
            state: $state
        );

        return $this->appendPermissionsToUrlIfNecessary(authUrl: $authUrl);
    }

    /**
     * Append the permissions to the authorization URL if necessary.
     *
     * @param string $authUrl
     * @return string
     */
    private function appendPermissionsToUrlIfNecessary(string $authUrl): string
    {
        return $this->permissions
            ? $authUrl . "&permissions=" . $this->permissions
            : $authUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null): array
    {
        $fields = parent::getCodeFields(state: $state);

        if (! $this->consent) {
            $fields['prompt'] = 'none';
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return self::DISCORD_TOKEN_URL;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(
            uri: self::DISCORD_USER_URL,
            options: [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode(json: (string) $response->getBody(), associative: true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw(user: $user)->map(
            attributes: [
                'id' => $user['id'],
                'nickname' => $this->formatNickname(user: $user),
                'name' => $user['username'],
                'email' => $user['email'] ?? null,
                'avatar' => $this->formatAvatar(user: $user),
            ]
        );
    }

    /**
     * Format the user nickname based on the username and discriminator.
     *
     * @param array $user
     * @return string
     */
    private function formatNickname(array $user): string
    {
        $username = $user['username'];
        $discriminator = $user['discriminator'] ?? '0';

        if ($discriminator !== '0') {
            return $username . '#' . $discriminator;
        }

        return $username;
    }

    /**
     * Format the user avatar URL.
     *
     * @param array $user
     * @return string|null
     */
    private function formatAvatar(array $user): ?string
    {
        if (empty($user['avatar'])) {
            return null;
        }

        $isGif = preg_match(pattern: '/a_.+/m', subject: $user['avatar']) === 1;
        $extension = $isGif ? 'gif' : 'png';

        return sprintf(
            '%s%s/%s.%s',
            self::DISCORD_AVATAR_URL,
            $user['id'],
            $user['avatar'],
            $extension
        );
    }
}
