<?php
    $driver         = "mysql";
    $host           = "localhost";
    $username       = "zigma";
    $password       = "?WSzvxHv1LGZ";
    $databasename   = "blue_planet_beta";   

  try {
      $conns = new PDO("mysql:host=$host;dbname=$databasename", $username, $password);
      $conns->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      return null; // Handle the error appropriately
  }