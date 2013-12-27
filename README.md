# ZfrOAuth2Server

ZfrOAuth2Server is a PHP library that aims to implement the OAuth 2 specification strictly. Contrary to other
libraries, it assumes you are using Doctrine, and provide various services based on Doctrine interfaces.

Currently, it's more of a proof of concept to implement a simpler and cleaner OAuth 2 server implementation. It
only implements the Resource Owner Password Credentials grant type and Client Credentials grant type (because...
well, that's the one I need for my project), but plan to implement others (or if you want to contribute!).

If you need a full featured project, that was tested by thousands of people, I suggest you to have a look
at one of those two PHP libraries:

- [OAuth2 Server from PHP-League](https://github.com/php-loep/oauth2-server)
- [OAuth2 Server from Brent Shaffer](https://github.com/bshaffer/oauth2-server-php)

## Requirements

- PHP 5.4 or higher
