# Парсер отчета 1C
Конвертакия текстового файла отчета 1C в формат XML и отправка его на сервер

**Использование:**

```php
<?php
require_once __DIR__."/ParseFile.php";

use ITTech\SmartINT\ParseFile;

ParseFile::init("kl_to_1c.txt")            // Инициализация
    ->xml(__DIR__."/tmp")                  // Директория сохранения xml
    ->set('http://tests.com/upload.php');  // Сайт для отправки XML
```
