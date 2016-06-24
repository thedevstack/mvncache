<?php
$mvnBaseUrl = 'http://repo1.maven.org/maven2';

$requestedArtifact = $_SERVER["REQUEST_URI"];
$requestedArtifact = str_replace('/mvn', '', $requestedArtifact);
$baseLocalFolder = __DIR__;
$localFolder = $baseLocalFolder.substr($requestedArtifact, 0, strripos($requestedArtifact, '/'));
//echo $localFolder."<br>";
if (!is_dir($localFolder)) {
  mkdir($localFolder, 0770, true);
}

$srcUrl = $mvnBaseUrl.$requestedArtifact;
//echo $srcUrl."<br>";
$src = fopen($srcUrl, 'r');

$dstPath = $baseLocalFolder.$requestedArtifact;
//echo $dstPath;
$dst = fopen($dstPath, 'w');

stream_copy_to_stream($src, $dst);

if (is_file($dstPath)) {
  chmod($dstPath, 0660);
  header('Location: http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
} else {
  header('HTTP/1.0 404 Not Found');
}
?>
