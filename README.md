# Arengu Auth PrestaShop module
This module enables custom signup, login and passwordless endpoints to interact with PrestaShop's authentication system from Arengu.

## Installation
1. Download the latest release ZIP file and **extract** it inside the `modules` directory of an existing PrestaShop installation. You should end with a file structure where you can locate the `ps_arengu_auth.php` file **precisely** in `modules/ps_arengu_auth/ps_arengu_auth.php`. Otherwise, the module will not be detected by PrestaShop.
2. Go to the admin panel and install from the module catalog, look in the "Other" category.

## Installation from repository (advanced)
1. Clone repository into a `ps_arengu_auth` directory inside the `modules` directory of an existing PrestaShop installation and `cd` into it.
2. Run `composer dump-autoload -o --no-dev`. There are no dependencies but the autoloader files **must** be generated.
3. Go to the admin panel and install from the module catalog, look in the "Other" category.

## Available endpoints

1. [Signup](#signup)
2. [Login](#login)
3. [Passwordless](#passwordless)
4. [Check existing email](#check-existing-email)

### Authentication

This module uses an API key to authenticate requests. You can view and manage your API key under your module settings. This API key **allows to impersonate any customer in your store, so you must keep it secret and do not share it in publicly accessible areas such as GitHub, client-side code, and so forth..**

Authentication to the API is performed via bearer authentication:

```
Authorization: Bearer YOUR_API_KEY
```

### Signup

Sign up users with email and password or just with an email (passwordless signup).

`POST` **/module/ps_arengu_auth/signup**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| firstname _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user first name. |
| lastname _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user last name. |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |
| password _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user plain password. If you don't provide a password a random one will be generated. This is useful if you want to use passwordless flows. |


#### Request sample

```json
{
  "firstname": "Jane",
  "firstname": "Doe",
  "email": "jane.doe@arengu.com",
  "password": "foobar"
}
```

#### Response headers sample

```curl
Set-Cookie: PrestaShop-f4f61db....=def502006807b076956f....; expires=Sun, DD-MM-YYYY HH:MM:ss GMT; Max-Age=1727998; path=/; domain=arengu.com; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "firstname": "Jane",
    "lastname": "Doe",
    "birthday": null,
    "id_gender": null,
    "company": null,
    "newsletter": null,
    "optin": null,
    "default_group": 3,
    "groups": [
      3
    ]
  }
}
```

### Login

Log in users with email and password.

`POST` **/module/ps_arengu_auth/login**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to sign up. |
| password _(required)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | Query selector or DOM element that the form will be appended to. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar"
}
```

#### Response headers sample

```curl
Set-Cookie: PrestaShop-f4f61db....=def502006807b076956f....; expires=Sun, DD-MM-YYYY HH:MM:ss GMT; Max-Age=1727998; path=/; domain=arengu.com; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "firstname": "Jane",
    "lastname": "Doe",
    "birthday": null,
    "id_gender": null,
    "company": null,
    "newsletter": null,
    "optin": null,
    "default_group": 3,
    "groups": [
      3
    ]
  }
}
```

### Passwordless

Authenticate users without password.

`POST` **/module/ps_arengu_auth/passwordless**

⚠️ **IMPORTANT** ⚠️ This endpoint is made to be used adding one authentication factor to verify the user identity (eg. one-time password via email or SMS, social login, etc).

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to authenticate. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com"
}
```

#### Response headers sample

```curl
Set-Cookie: PrestaShop-f4f61db....=def502006807b076956f....; expires=Sun, DD-MM-YYYY HH:MM:ss GMT; Max-Age=1727998; path=/; domain=arengu.com; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "firstname": "Jane",
    "lastname": "Doe",
    "birthday": null,
    "id_gender": null,
    "company": null,
    "newsletter": null,
    "optin": null,
    "default_group": 3,
    "groups": [
      3
    ]
  }
}
```

### Check existing email

Check if an email exists in your database.

`POST` **/module/ps_arengu_auth/checkemail**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com"
}
```

#### Response sample

```json
{
  "email_exists": true
}
```

## Contributing
1. Clone repository into a `ps_arengu_auth` directory inside the `modules` directory of an existing PrestaShop installation and `cd` into it.
2. Run `composer install --dev`.

## Coding standards
We follow PrestaShop's own [coding standards](https://devdocs.prestashop.com/1.7/development/coding-standards/). `php-cs-fixer` can help checking and fixing code by running `vendor/bin/php-cs-fixer fix . --config=../../.php_cs.dist` directly from the repository directory.
