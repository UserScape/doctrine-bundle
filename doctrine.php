<?php

require 'start.php';

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\DBAL\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;

$em = IoC::resolve('doctrine.manager');

$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($em->getConnection()),
    'em' => new EntityManagerHelper($em)
));

ConsoleRunner::run($helperSet);