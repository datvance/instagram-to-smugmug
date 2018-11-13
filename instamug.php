<?php
set_time_limit(0);

require './vendor/autoload.php';

$migrator = new Josheli\InstamugMigrator();

try
{
  $migrator->run();
}
catch(Exception $e)
{
  echo $e->getMessage();
}