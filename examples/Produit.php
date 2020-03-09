<?php

require 'Service/ServiceProduit.php';

$connect = mysqli_connect("localhost", "dixital", "dixital", "quincaillerieGervais");
//Get JsonFIle
$service = new ServiceProduit;
$scans = scandir('JSON/',1);
$scans = array_splice($scans, 1, -2);




//Browse Folder

foreach ($scans as $scan)
{
    //init
    $pathTXT = "";
    $imageAllTXT = "";

    $json = $service->getJSON($scan);
    //Get and Split Content :
    $url = $json['URL :'];
    $code = $json['Code :'];
    $path = $json['Path :'];
    $price = $json['Price :'];
    $name = $json['Name :'][0];
    $description = $json['Description :'][0];
    $catalogue = $json['Catalogue :']['Catalogue'];
    $cataloguePAGE = $json['Catalogue :']['Page'];
    $catalogueCODE = $json['Catalogue :']['Code catalogue'];
    $table = $json['Table :'];
    $iconSRC = $json['Icon :'][0]['src'];
    $iconALT = $json['Icon :'][0]['alt'];
    $imageShowHREF = $json['Image Show :'][0]['href'];
    $imageShowALT = $json['Image Show :'][0]['alt'];
    $allImage = $json['All Image :'];
    $feature = $json['Feature :'];

    echo "cata :   ".$catalogueCODE."\n\r";




    //Conversion in String :
    $price = str_replace(".", ",", $price);

    foreach($path as $p)
    {
        $pathTXT = $pathTXT."/".$p;
    }

    foreach($allImage as $allImg)
    {
        $imageAllTXT = $imageAllTXT.$allImg." SEPARATION ";
    }









    //insertion in base :
//    $result = $connect->query('DELETE FROM Produits');
//    $result = $connect->query('INSERT INTO Produits (isbn,url,price) VALUE ("'.$code.'","'.$url.'","'.$price.'")');




}



