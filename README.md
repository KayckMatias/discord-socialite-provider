# Discord Socialite Provider for Laravel

![Packagist](https://img.shields.io/packagist/v/kayckmatias/discord-socialite-provider)
![License](https://img.shields.io/packagist/l/kayckmatias/discord-socialite-provider)

A Discord OAuth2 provider for the Laravel Socialite package. This package allows you to easily integrate Discord login with Laravel applications using Socialite with extra features for Discord OAuth2.

## Installation
1. Install the package via Composer:

   ```bash
   composer require kayckmatias/discord-socialite-provider
   ```

2. Add the service provider to the config/app.php **(only if you're using Laravel version <5.5 or not using package auto-discovery):**

   ```php
   'providers' => [
        KayckMatias\Laravel\Socialite\DiscordServiceProvider::class,
    ],
   ```

3. Add your Discord credentials to the config/services.php file:

    ```php
    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => '/auth/discord/callback',
    ]
    ```

## Configuration
You need to register your application with Discord to obtain the credentials required for OAuth2. You can register a new application at the [Discord Developer Portal](https://discord.com/developers/applications).

Once you have your credentials, add the following environment variables to your `.env` file:

```env
DISCORD_CLIENT_ID=your-client-id
DISCORD_CLIENT_SECRET=your-client-secret
```

## Usage
Once the package is installed and configured, you can use Discord as a provider with Laravel Socialite.

### Example Redirecting to Discord
In your controller, use the Socialite facade to redirect the user to the Discord authentication page:

```php
use Laravel\Socialite\Facades\Socialite;

public function redirectToDiscord()
{
    return Socialite::driver('discord')->redirect();
}
```

### Handling the Callback
Once the user authorizes the app, Discord will redirect the user back to your application's callback URL. You can handle the callback and retrieve user information as follows:

```php
use Laravel\Socialite\Facades\Socialite;

public function handleDiscordCallback()
{
    $user = Socialite::driver('discord')->user();

    // Access user details
    $id = $user->getId();
    $name = $user->getName();
    $nickname = $user->getNickname();
    $email = $user->getEmail();
    $avatar = $user->getAvatar();

    // Handle the user data
}
```

## Advanced Features

### Custom Scopes
To request with custom scopes, you can use the `setScopes` method:

```php
return Socialite::driver('discord')
    ->setScopes(['identify', 'email', 'bot'])
    ->redirect();
```

### Adding Permissions
To request additional permissions, you can use the `withPermissions` method:

```php
return Socialite::driver('discord')
    ->withPermissions('1689934340028480')
    ->redirect();
```

### Bot Scopes
If you're working with a bot, you can use `asBot` to bot scope instead default `identify` scope:

```php
return Socialite::driver('discord')
    ->asBot()
    ->redirect();
```

### Specify Guild
To add the `guild_id=` parameter to the Discord authorization URL, use the `withGuildId` method:

```php
return Socialite::driver('discord')
    ->withGuildId('0000000000000000000')
    ->redirect();
```

### Consent Prompt
To add the `prompt=none` parameter to the Discord authorization URL, use the `withConsent` method:

```php
return Socialite::driver('discord')
    ->withConsent()
    ->redirect();
```

## License
This package is open-source and licensed under the MIT license.


## More Information
For more information about Discord OAuth, please visit the [Discord OAuth2 Documentation](https://discord.com/developers/docs/topics/oauth2).
For more information about Socialite, please visit the [Socialite Documentation](https://laravel.com/docs/socialite).

## Issues
For any issues, feel free to open a ticket on the [GitHub repository](https://github.com/KayckMatias/discord-socialite-provider/issues).

<center>Made with love, I hope it helps someone :)</center>