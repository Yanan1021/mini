<?php

if (!defined('IN_MINI')) {
    exit();
}

class Base {

    public function __call($method, array $args){

        echo 'Method:'.$method.'() is not exists in Class:'.get_class($this).'!<br/>The args is:<br/>';
        foreach ($args as $value) {
            echo $value, '<br/>';
        }
    }

}
