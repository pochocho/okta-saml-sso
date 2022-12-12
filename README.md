# Okta SAML SSO for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pochocho/okta-saml-sso.svg?style=flat-square)](https://packagist.org/packages/pochocho/okta-saml-sso)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/pochocho/okta-saml-sso/run-tests?label=tests)](https://github.com/pochocho/okta-saml-sso/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/pochocho/okta-saml-sso/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/pochocho/okta-saml-sso/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pochocho/okta-saml-sso.svg?style=flat-square)](https://packagist.org/packages/pochocho/okta-saml-sso)

This package provide a simple to use implementation for implementing Okta SAML Login to the app. The package assists in receiving the SAML Response and Getting the

# Installation

```
composer require pochocho/okta-saml-sso
```

Generate your certificate, if you want to generate a self signed certificate, you can follow the command as an example:

> Note: by default, this package expects the certificate and key to be at the root of your projects directory. **Remember to add both files to .gitignore**

```
openssl req -x509 -newkey rsa:2048 -nodes -keyout oktasso.key -out oktasso.crt -days 365
```

# Creating and Configuring Okta App

Create your Okta app, Select SAML 2.0 as the Sign-in Method.

in the form the `Single sign on URL` refers to url in your app that Okta will send a `POST` request to after successful authentication.

`Audience URI` is the URL where you are publishing public information about its SAML configuration (the metadata)

`Default RelayState` is the URL that Okta will redirect to after successfull login.

On this form you can also set the `Attribute Statements` and map them to profile fields. This package assumes snake_case naming conventions on the attributes (e.g first_name, las_name, email, etc)
 
After configuring the app, visit the signon tab and click on the "View SAML Setup Instructions", once the page loads download the certificate file and place it in the root of your application call it `idp.cert` **important: do not commit this file to version control** 

## configuration

The following values must be set in hour `.env`

`OKTA_SIGNON_URL`: You can get this value from your Okta Admin Dashboard, by going to the "Sign On" tab on your SAML app and clicking on the "View SAML setup Instructions" button. Use the "Identity Provider Single Sign-On URL" value.

`LOGIN_REDIRECT_ROUTE`:This is the route name where you want your users to be redirected logging in.

You can also publish the configuration file by running

```
php artisan vendor:publish --tag=okta-saml-sso
```

The package assumes that a cert and key file exist at the root directory of the project, named oktasso.crt and oktasso.key:

-   `OKTA_CERTIFICATE_PATH` path to the certificate file
-   `OKTA_KEY_PATH` path to the key file
-   `OKTA_ATTRIBUTE_STATEMENTS` Comma separated list of attribute statements setup in Okta (default value: `'first_name','last_name','email','groups'`)

If your application does not use the User model for authentication, you can configure the model with the `OKTA_AUTHENTICATABLE_MODEL` key on the env.

# Usage

## Provided

The easiest way to use the package is by using the provided controllers. The package provides 2 controllers one if you are using encryption, and another if you are not. To get started register the route in the `web.php` routes file

```php
Route::post('/login', \Pochocho\OktaSamlSso\Http\Controllers\EncryptedLoginController::class)->name('login');
```

> **NOTE:** Since Okta sends a post request to the app once authentication is completed, we need to ignore the login route from csrf validation. You can do this by adding the login url to the `VerifyCsrfToken` middleware in the application.

Register the `SsoAuthenticate` Middleware in the Http Kernel. You can either substitute the `auth` middleware or create your own.

```php
'auth' => \Pochocho\OktaSamlSso\Http\Middleware\SsoAuthenticate::class,
```

Add the new middleware to your auth protected routes, try loading a proteted route and you should be redirected to the Okta login flow.

## Custom

You can implement your own controller and use the `OktaSaml` class to handle the assertions from the Okta SAML Response. The class provides two methods one for un-encrypted SAML Responses `$oktaSaml->getEntity()` and another for encrypted responses `$oktaSaml->getEncryptedEntity()`.

The OktaSaml Class is bound to the IoC Container and can be resolved through dependency injection or by using `app()->make(Pochocho\OktaSamlSso\OktaSaml::class);`
