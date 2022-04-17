# Geohome

## Sample usage without namespace
```php
<?php

include 'class.php';

$password = '';
$username = '';

$geo = new Geohome($username, $password);

$data = $geo->live();

print_r($data);
print_r(date('Y-m-d H:i:s', $data->latestUtc));
```
