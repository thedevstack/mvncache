<?php
/*
 * Configuration file for mvn cache
 */
 
return [
  'mavenBaseUrls' => [
    'https://repo1.maven.org/maven2',
    'https://jcenter.bintray.com',
    'https://jitpack.io/',
    'https://repo.maven.apache.org/maven2/',
    'https://oss.sonatype.org/content/repositories/snapshots/',
    'https://oss.sonatype.org/content/repositories/releases/',
    'https://plugins.gradle.org/m2' //Gradle plugin repo
    ],
  'logfile' => 'logs/mvncache.log',
];
?>