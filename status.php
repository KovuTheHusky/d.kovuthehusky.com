<?php

if (php_sapi_name() !== 'cli') {
  exit;
}

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
libxml_use_internal_errors(true);

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\SourceQuery\SourceQuery;

require __DIR__ . '/configuration.php';
require __DIR__ . '/node_modules/PHP-Minecraft-Query/src/MinecraftQuery.php';
require __DIR__ . '/node_modules/PHP-Minecraft-Query/src/MinecraftQueryException.php';
require __DIR__ . '/node_modules/PHP-Source-Query/SourceQuery/bootstrap.php';



// Minecraft

$mq = new MinecraftQuery();
$rcon = new SourceQuery();

// CSGO - Practice

$cpq = new SourceQuery();

// CSGO - Retake

$crq = new SourceQuery();



while (true) {
  $time = time();
  while ($time == time()) {
    usleep(1000);
  }

  // Minecraft
  try {
    $mq->Connect('10.0.1.4', 25565, 1);
    
    $info = $mq->GetInfo();
    $players = $mq->GetPlayers();

    $old = json_decode(file_get_contents(__DIR__ . '/status/minecraft.json'));
    $tics = $old->Tickrate;
    if (!is_array($tics)) {
      $tics = array();
    }
    $mems = $old->Memory;
    if (!is_array($mems)) {
      $mems = array();
    }
    if (count($tics) >= 60) {
      array_shift($tics);
    }
    if (count($mems) >= 60) {
      array_shift($mems);
    }

    $rcon->Connect('10.0.1.4', 25575, 1, SourceQuery::SOURCE);
    $rcon->SetRconPassword(MINECRAFT_TOKEN);
    $lagmeter = explode(PHP_EOL, $rcon->Rcon('lm'));
    $tickrate = (double) explode(' ', $lagmeter[0])[2];
    $tics[] = $tickrate;
    $memory = explode(' ', $lagmeter[1])[3];
    $memory = (double) substr($memory, 1, strlen($memory) - 3);
    $mems[] = $memory;

    if ($info === false) {
      file_put_contents(__DIR__ . '/status/minecraft.json', '');
    } else {
      if ($players === false) {
        $players = array();
      }
      $json = array('Info' => $info, 'Players' => $players, 'Tickrate' => $tics, 'Memory' => $mems);
      $json = json_encode($json);
      file_put_contents(__DIR__ . '/status/minecraft.json', $json);
    }
  } catch (Exception $e) {
  }

  // Terraria

  $data = @file_get_contents('http://10.0.1.5:7878/status?players=true');
  if ($data === false) {
    file_put_contents(__DIR__ . '/status/terraria.json', '');
  } else {
    file_put_contents(__DIR__ . '/status/terraria.json', $data);
  }

  // CSGO - Practice

  try {
    $cpq->Connect('10.0.1.5', 27015, 1, SourceQuery::SOURCE);

    $info = $cpq->GetInfo();
    $players = $cpq->GetPlayers();

    $json = array('Info' => $info, 'Players' => $players);
    $json = json_encode($json);
    file_put_contents(__DIR__ . '/status/counterstrike-practice.json', $json);
  } catch (Exception $e) {
  }
  // CSGO - Retake

  try {
    $crq->Connect('10.0.1.5', 27025, 1, SourceQuery::SOURCE);

    $info2 = $crq->GetInfo();
    $players2 = $crq->GetPlayers();

    $json2 = array('Info' => $info2, 'Players' => $players2);
    $json2 = json_encode($json2);
    file_put_contents(__DIR__ . '/status/counterstrike-retake.json', $json2);
  } catch (Exception $e) {
  }

}
