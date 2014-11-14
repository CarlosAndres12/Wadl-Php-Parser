<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 12/11/2014
 * Time: 07:31 PM
 */

include_once 'clases/WadlApplication.php';
/*
 include("WadlTest.php");
$wadlTest = new WadlTest();
$wadlTest->testParseWadl();
 */

$xml=simplexml_load_file("test2.xml") or die("Error: Cannot create object");
//print_r($xml);
//echo (string)$xml->resources['base'];
//echo "<br>";
//echo (string)$xml->resource_type['id'];

//var_dump($xml->resources->resource);
//echo "<br>";







$a=WadlApplication::loadXml($xml, "test");
echo 'bien';