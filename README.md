# Alternative Admin for Moodle

[![GitHub Release](https://img.shields.io/github/v/release/ManuelGil/alternate-admin)]()
[![GitHub Release Date](https://img.shields.io/github/release-date/ManuelGil/alternate-admin)]()
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

![preview](https://raw.githubusercontent.com/ManuelGil/alternate-admin/main/docs/images/preview.png)

This wrapper for Moodle adds a new interface to streamline your administrative tasks.

## Features

-   Bootstrap 4 Admin Dashboard Template
-   Simple Vue.js 2 CDN integration
-   Friendly Alerts via Sweet Alert
-   Easy installation via Composer
-   Friendly URLs
-   Integration with Mustage Engine
-   Gravatar Profile image

## Requirements

-   PHP 7.2 or later
-   MySQL or MariaDB
-   Apache Server
-   Moodle 3.x Installation

## Installation

### Copy this project

1. Clone or Download this repository
2. Unzip the archive if needed
3. Copy the folder in the path of moodle
4. Rename the folder if needed
5. Start a Text Editor (Atom, Sublime, Visual Studio Code, Vim, etc)
6. Add the project folder to the editor

### Install the project

You can install this wrapper via composer with the following commands:

#### _Development_

-   Required a composer installation.

```bash
$ composer install
```

-   Downloading [composer.phar](https://getcomposer.org/download/).

```bash
$ sudo php composer.phar install
```

#### _Production_

-   Required a composer installation.

```bash
$ composer install --no-dev --optimize-autoloader
```

-   Downloading [composer.phar](https://getcomposer.org/download/).

```bash
$ sudo php composer.phar install --no-dev --optimize-autoloader
```

## Configure the project

-   Copy the [`.env.example`](./.env.example)
    file and call it `.env`.

```bash
$ cp .env.example .env
```

-   Edit the environment variables in the .env file as you need.

    > MODE_DEBUG => show errors

    > DOMAIN => moodle installation domain (required)

    > MDL_CONFIG => moodle config file path (required)

    > COMPANY => company name (displayed in page title)

-   Create file `error.log` in folder `logs`.

-   Make www-data the owner to `logs` folder.

```bash
$ sudo chown www-data: logs/
```

-   Reset apache2 service.

## Changelog

See [CHANGELOG.md](./CHANGELOG.md)

## Contributing

Thank you for considering contributing to alternative admin. The contribution guide can be found in the [CONTRIBUTING.md](./.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the alternate admin community is welcoming to all, please review and abide by the [CODE_OF_CONDUCT](./.github/CODE_OF_CONDUCT.md).

## Authors

-   **Manuel Gil** - _Owner_ - [ManuelGil](https://github.com/ManuelGil)

See also the list of [contributors](https://github.com/ManuelGil/alternate-admin/contributors)
who participated in this project.

## License

Alternate Admin is licensed under the GPL v3 or later License - see the
[GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html) for details.
