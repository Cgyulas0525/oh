<?php
require __DIR__ . "/inc/bootstrap.php";
require PATH_FILES . "/dataManipulation.php";
require PATH_FILES . "/fillArray.php";

include PATH_DATA . "/data.php";
include PATH_DATA . "/universityData.php";

$exampleArray = [[ "data" => $exampleData, "name" => "exampleData" ],
                 [ "data" => $exampleData4, "name" => "exampleData4" ],
                 [ "data" => $exampleData2, "name" => "exampleData2" ],
                 [ "data" => $exampleData3, "name" => "exampleData3" ]];


$fillArray = new FillArray();
$fillArray->fillUniversity($ELTE);
$fillArray->fillUniversity($PPKE);

foreach ($exampleArray as $examp) {
    $dataManipulation = new dataManipulation($examp['data'], $examp["name"]);
    echo $dataManipulation->basicPointsCalculation($fillArray);
}




