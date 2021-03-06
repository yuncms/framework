# Framework For YUNCMS
 
YUNCMS 底层框架，基于 YII2 二次封装。

[![License](https://poser.pugx.org/yuncms/framework/license.svg)](https://packagist.org/packages/yuncms/framework)
[![Latest Stable Version](https://poser.pugx.org/yuncms/framework/v/stable.png)](https://packagist.org/packages/yuncms/framework)
[![Total Downloads](https://poser.pugx.org/yuncms/framework/downloads.png)](https://packagist.org/packages/yuncms/framework)
[![Build Status](https://img.shields.io/travis/yuncms/framework.svg)](http://travis-ci.org/yuncms/framework)
[![Code Coverage](https://scrutinizer-ci.com/g/yuncms/framework/badges/coverage.png?s=31d80f1036099e9d6a3e4d7738f6b000b3c3d10e)](https://scrutinizer-ci.com/g/yuncms/framework/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/yuncms/framework/badges/quality-score.png?s=b1074a1ff6d0b214d54fa5ab7abbb90fc092471d)](https://scrutinizer-ci.com/g/yuncms/framework/)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist yuncms/framework
```

or add

```json
"yuncms/framework": "~1.0.0"
```

to the `require` section of your composer.json.

## Task

```shell
* * * * * /path/to/yii cron/minute >/dev/null 2>&1
*/5 * * * * /path/to/yii cron/minute >/dev/null 2>&1
0 * * * * /path/to/yii cron/hourly >/dev/null 2>&1
0 0 * * * /path/to/yii cron/daily >/dev/null 2>&1
0 0 * * 0 /path/to/yii cron/month >/dev/null 2>&1
0 0 1 * *
0 0 1 1 *

```

## License

This is released under the LGPL-3.0 License. See the bundled [LICENSE.md](LICENSE.md)
for details.