<?php

define('IN_MINI', true);

define('APP_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once APP_ROOT.'miniphp/Mini.class.php';

mini::run();