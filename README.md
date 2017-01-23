# codeception-yii2-populate
This module restore the dump before each test for your `yii2` `acceptance` `test`

> Note: Need more? See [bscheshirwork/codeception-db-yii2-config](https://github.com/bscheshirwork/codeception-db-yii2-config)

Example `backend/tests/acceptance.suite.yml`
```
class_name: AcceptanceTester
modules:
    enabled:
# See docker-codeception-run/docker-compose.yml: "ports" of service "nginx" is null; the selenium service named "firefox"
# See nginx-conf/nginx.conf: listen 80 for frontend; listen 8080 for backend
        - WebDriver:
            url: http://nginx:8080/
            host: firefox
            port: 4444
            browser: firefox
        - Yii2:
            part:
              - email
              - ORM
              - Fixtures
        - \bscheshirwork\Codeception\Module\Yii2Populate:
            dump: ../common/tests/_data/dump.sql #relative path from "codeception.yml"
```

#Installation
Add to you test environment`composer.json`
```
    "require": {
        "bscheshirwork/codeception-yii2-populate": "*"
    }
```
