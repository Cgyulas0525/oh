<?php
require __DIR__ . "/inc/bootstrap.php";
require PATH_FILES . "/dataManipulation.php";
require PATH_FILES . "/fillArray.php";

include PATH_DATA . "/data.php";
include PATH_DATA . "/universityData.php";

$fillArray = new FillArray();
$fillArray->fillUniversity($ELTE);
$fillArray->fillUniversity($PPKE);

$dataManipulation = new dataManipulation($exampleData);
$dataManipulation->basicPointsCalculation($fillArray);

$dataManipulation = new dataManipulation($exampleData4);
$dataManipulation->basicPointsCalculation($fillArray);
$dataManipulation = new dataManipulation($exampleData2);
$dataManipulation->basicPointsCalculation($fillArray);
$dataManipulation = new dataManipulation($exampleData3);
$dataManipulation->basicPointsCalculation($fillArray);


