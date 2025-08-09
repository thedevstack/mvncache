<?php
/*
 * Configuration file for mvn cache
 */
 
return [
  'mavenBaseUrls' => [
    'https://git.fucktheforce.de/api/packages/thedevstack/maven',
    'https://repo1.maven.org/maven2',
    'https://jcenter.bintray.com',
    'https://jitpack.io/',
    'https://repo.maven.apache.org/maven2/',
    'https://oss.sonatype.org/content/repositories/snapshots/',
    'https://oss.sonatype.org/content/repositories/releases/',
    'https://plugins.gradle.org/m2', //Gradle plugin repo
    'https://dl.google.com/dl/android/maven2/',
    'http://central.maven.org/maven2/'
    ],
  'logfile' => 'logs/mvncache.log',
];
?>
