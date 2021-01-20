# Alternative Admin for Moodle

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![stability-stable](https://img.shields.io/badge/stability-stable-green.svg)](https://github.com/emersion/stability-badges#stable)

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

-   _Development_

```bash
$ sudo php composer.phar install
```

or

```bash
$ composer install
```

-   _Production_

```bash
$ sudo php composer.phar install --no-dev --optimize-autoloader
```

or

```bash
$ composer install --no-dev --optimize-autoloader
```

## Configure the project

-   Copy the [`.env.example`](https://github.com/ManuelGil/alternate-admin/blob/master/.env.example)
    file and call it `.env`.

-   Create file `error.log` in folder `logs`.

-   Make www-data the owner to `logs` folder.

```bash
$ sudo chown www-data: logs/
```

-   Reset apache2 service.

## Contributing

Thank you for considering contributing to alternative admin. The contribution guide can be found in the [CONTRIBUTING.md](https://github.com/ManuelGil/alternate-admin/blob/master/.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the alternate admin community is welcoming to all, please review and abide by the [CODE_OF_CONDUCT](https://github.com/ManuelGil/alternate-admin/blob/master/.github/CODE_OF_CONDUCT.md).

## Authors

-   **Manuel Gil** - _Owner_ - [ManuelGil](https://github.com/ManuelGil)

See also the list of [contributors](https://github.com/ManuelGil/alternate-admin/contributors)
who participated in this project.

## License

Alternate Admin is licensed under the GPL v3 or later License - see the
[GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html) for details.
