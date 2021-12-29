# 友盟统计

```
composer require limingxinleo/umeng-track
```

## 使用

```php
<?php
use UMeng\Track\Client;

$client = new Client('xxx');

$client->getAppList();

$client->getPlanList('appid');
```
