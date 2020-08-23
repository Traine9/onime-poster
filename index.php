<?php
require __DIR__ . '/vendor/autoload.php';
use Kreait\Firebase\Factory;

$servername = "f80b6byii2vwv8cx.chr7pe7iynqr.eu-west-1.rds.amazonaws.com";
$username = "wrdo12m7ecr8u77p";
$password = "password";

try {
  $conn = new PDO("mysql:host=$servername;dbname=fdg8hfjnr3bnca2t", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$lastDate = $conn->query('SELECT value FROM value WHERE id =1');
print_r($lastDate);