<?php
require_once __DIR__."/ParseFile.php";

use ITTech\SmartINT\ParseFile;

ParseFile::init("kl_to_1c.txt")
    ->xml(__DIR__."/tmp")
    ->set('http://tests.com/upload.php');