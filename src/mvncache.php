<?php

include_once(__DIR__.'/lib/functions.http.inc.php');
include_once(__DIR__.'/lib/error.handler.inc.php');
// Load configuration
$config = require(__DIR__.'/config/config.inc.php');

initTheDevStackErrorHandler($config["logfile"]);

$requestedArtifact = $_SERVER["REQUEST_URI"];
$requestedArtifact = preg_replace('/^\/mvn/', '', $requestedArtifact);
$baseLocalFolder = __DIR__.'/..';
$localFolder = $baseLocalFolder.substr($requestedArtifact, 0, strripos($requestedArtifact, '/'));

if ('' == $requestedArtifact) { // no artifact is requested - return
  sendHttpReturnCodeAndMessage(403, 'Not allowed');
}

$tempFile = tmpfile();
foreach ($config['mavenBaseUrls'] as $mvnBaseUrl) {
  $srcUrl = $mvnBaseUrl.$requestedArtifact;
  //echo $srcUrl."<br>";
  $src = fopen($srcUrl, 'r');
  $found = FALSE !== $src;
  
  if ($found) {
    $filesize = @stream_copy_to_stream($src, $tempFile);
    $found = 0 < $filesize;
    $metaData = @stream_get_meta_data($src);
    $headers = [];
    $headerNames = [
      'Content-Type',
      'Content-Length',
      'ETag',
      'Last-Modified',
    ];
    
    if (isset($metaData) && array_key_exists('wrapper_data', $metaData)) {
      foreach ($metaData['wrapper_data'] as $header) {
        foreach ($headerNames as $headername) {
          if (stripos($header, $headername) === 0) {
            $headerValue = trim(substr($header, strlen($headername) + 1)); // +1 to remove the colon
            $headers[$headername] = $headerValue;
          }
        }
      }
    }
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
  // Rest temp file pointer
  fseek($tempFile, 0);

  if (preg_match('/maven-metadata.xml/', $requestedArtifact) || preg_match('/-SNAPSHOT/', $requestedArtifact)) {
    trigger_error("Found artifact not to cache: ".$requestedArtifact, E_USER_NOTICE);
    foreach ($headers as $header => $value) {
      if (is_array($value)) {
        $value = implode(', ', $value);
      }
      header($header.': '.$value);
    }

    stream_copy_to_stream($tempFile, fopen('php://output', 'w'));
  } else {
    trigger_error("Found artifact to cache: ".$requestedArtifact, E_USER_NOTICE);
    //echo $localFolder."<br>";
    if (!is_dir($localFolder)) {
      mkdir($localFolder, 0770, true);
    }
    $dstPath = $baseLocalFolder.$requestedArtifact;
    //echo $dstPath;
    $dst = fopen($dstPath, 'w');

    stream_copy_to_stream($tempFile, $dst);
    if (is_file($dstPath)) {
      chmod($dstPath, 0660);
      header('Location: '.getServerProtocol().'://'.getRequestHostname().$_SERVER["REQUEST_URI"]);
    } else {
      header('HTTP/1.0 404 Not Found');
    }
  }
}
?>
