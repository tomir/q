<?php

$this->group('/crud', function () {
    include(__DIR__ . '/Routing/crud.php');
});

$this->group('/tff', function () {
    include(__DIR__ . '/Routing/tff.php');
});

$this->group('/chat', function () {
    include(__DIR__ . '/Routing/chat.php');
});
