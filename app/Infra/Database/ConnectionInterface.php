<?php

namespace App\Infra\Database;

interface ConnectionInterface
{
    function connect();
    function query($sql, $params = []);
    function escapeString($string);
    function close();
}
