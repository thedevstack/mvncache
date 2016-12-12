<?php

include_once(__DIR__.'/lib/functions.http.inc.php');
include_once(__DIR__.'/lib/error.handler.inc.php');
// Load configuration
$config = require(__DIR__.'/config/config.inc.php');

initTheDevStackErrorHandler($config["logfile"]);

$requestedArtifact = $_SERVER["REQUEST_URI"];
$requestedArtifact = str_replace('/mvn', '', $requestedArtifact);
$baseLocalFolder = __DIR__.'/..';
$localFolder = $baseLocalFolder.substr($requestedArtifact, 0, strripos($requestedArtifact, '/'));

$tempFile = tmpfile();
foreach ($config['mavenBaseUrls'] as $mvnBaseUrl) {
  $srcUrl = $mvnBaseUrl.$requestedArtifact;
  //echo $srcUrl."<br>";
  $src = fopen($srcUrl, 'r');
  $found = FALSE !== $src;
  
  if ($found) {
    $filesize = @stream_copy_to_stream($src, $tempFile);
    $found = 0 < $filesize;
  }
  if ($found) {
    trigger_error("Artifact found at $srcUrl", E_USER_NOTICE);
    break;
  } else {
    trigger_error("Artifact NOT found at $srcUrl", E_USER_WARNING);
  }
}

if (!$found) {
  trigger_error("Artifact NOT found at any source", E_USER_WARNING);
  sendHttpReturnCodeAndMessage(404, 'Not found');
} else {
  //echo $localFolder."<br>";
  if (!is_dir($localFolder)) {
    mkdir($localFolder, 0770, true);
  }
  $dstPath = $baseLocalFolder.$requestedArtifact;
  //echo $dstPath;
  $dst = fopen($dstPath, 'w');
  fseek($tempFile, 0);
  stream_copy_to_stream($tempFile, $dst);
  if (is_file($dstPath)) {
    chmod($dstPath, 0660);
    header('Location: http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
  } else {
    header('HTTP/1.0 404 Not Found');
  }
}
?>
