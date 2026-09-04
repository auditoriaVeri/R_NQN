<?php
/**
 * Created by PhpStorm.
 * User: ddolz
 * Date: 05/02/2019
 * Time: 15:10
 */


require_once("sftpFunc.php");

if (ArchivoExistente("20Oct2016101136_2AA454HU.pdf") != 1)
  echo "no ta";
else
  echo "si ta";