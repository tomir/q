<?php

$this->group('/crud', function () {
    include(__DIR__ . '/Routing/crud.php');
});

$this->group('/form', function () {
    include(__DIR__ . '/Routing/form.php');
});

$this->group('/chat', function () {
    include(__DIR__ . '/Routing/chat.php');
});

$this->group('/auth', function () {
    include(__DIR__ . '/Routing/auth.php');
});
