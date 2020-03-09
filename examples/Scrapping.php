<?php

declare(strict_types=1);

use Symfony\Component\Crawler;

require 'Service/ServiceScrapping.php';
require __DIR__ . '/../vendor/autoload.php';
echo "0";
$client = \Symfony\Component\Panther\Client::createChromeClient();
$crawler = $client->request('GET', 'http://www.quincailleriegervais.fr/');
echo"1";
$link = $crawler->selectLink('Produits')->link();

$crawler = $client->click($link);


$client->waitFor('.sub-nav');

$service = new ServiceScrapping;
$service->SetCrawler($crawler);
$links = $service->getHref('ul.level-wrapper > li.menu-item-level-0 > a:first-child');

foreach ($links as $link)
{
    $counterPage = 0;
    $limitPage = "1";

    while ($limitPage != null) {
        $counterPage++;
        $goLinks = $link . "?page=" . $counterPage;
        $crawler = $client->request('GET', $goLinks);
        $service->SetCrawler($crawler);

        $limitPage = $service->getHref('ul > li.shorthands > a.lastpage');

        $elements = $service->getHref('ul.products > li.block-product > div.link-wrap > a.btn');

        foreach ($elements as $element) {
            $goElements = "" . $element;
            $crawler = $client->request('GET', $goElements);

            $service->SetCrawler($crawler);

            //init :
            $getTableBody = null;
            $getTableHead = null;
            $getTable = null;
            $state = "";


            //---------------------------------------------------GET CONTENT--------------------------------------------------//


            $getName = $service->getText('div.main > div.product-detail > h2');
            $getDescription = $service->getText('div.main > div.product-detail > div.description > p');
            $getCatalogue = $service->getText('div.main > div.product-detail > div.infos-produit > div.infos-catalogue > p');
            $getPath = $service->getText('div.main > div.breadcrumb > ol > li > a > span');
            $getIcon = $service->getSrcAlt('div.main > div.product-detail > div.brand > a > img');
            $getImageShow = $service->getHrefAlt('div.main > div.product-detail > div.image > a');
            $getImageAll = $service->getHref('div.main > div.product-detail > div.image > div.visuels > ul > li > a');
            $getTableHead = $service->getText('div.main > table.articles-list > thead > tr > th');
            $getTableBody = $service->getTableBodyS();
            $getFeature = $service->getSrcAlt('div.features > ul > li > a > img');
            $getImageShowHref = $service->getHref('div.main > div.product-detail > div.image > a');
            $getIconSrc = $service->getSrc('div.main > div.product-detail > div.brand > a > img');
            $getFeatureSrc = $service->getSrc('div.features > ul > li > a > img');



            //---------------------------------------------------Manipulation--------------------------------------------------//


            //Price to Float :
            $price = $service->getPrice($getTableBody);

            //Slice Catalogue :
            $rTableCatalogue = $service->getTableCatalogueS($getCatalogue);

            //Insert TableHead and TableBody into Table :
            $getTable = $service->getTableS($getTableHead, $getTableBody);

            //Get Code URL :
            $code = $service->getCode($element);

            //Insert Name inside Path :
            array_push($getPath, $getName[0]);

            //get icon code :
            $codeIco = $service->getLast($getIconSrc);

            //get image code :
            $codeImg = $service->getLast($getImageShowHref);



            //---------------------------------------------------------Folder--------------------------------------------------------//


            //Folder Name :
            $mainFolderName = 'JSON';
            $imagesFolderName = 'JSON\Images';
            $codeFolderName = 'JSON\Images/'.$code;
            //Create Folder:
            $service->createFolder($mainFolderName);
            $service->createFolder($imagesFolderName);
            $service->createFolder($codeFolderName);



            //---------------------------------------------------------File--------------------------------------------------------//

            //init :
            $fileName = "" . $code;

            //----------------------------------------JSON
            //Convert all contents to JSON :
            $getAll = json_encode(array('URL :' => $element, 'Code :' => $code, 'Path :' => $getPath, 'Price :' => $price, 'Name :' => $getName,
                'Description :' => $getDescription, 'Catalogue :' => $rTableCatalogue, 'Table :' => $getTable, 'Icon :' => $getIcon, 'Image Show :' => $getImageShow,
                'All Image :' => $getImageAll, 'Feature :' => $getFeature),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            //Create Json :
            $fileName = "" . $code;
            file_put_contents("JSON/$fileName.json", $getAll);

            $state = $state."A";



            //----------------------------------------IMAGE
            //Get Image :
            if($getImageShowHref[0] != null) {
                $img_content = file_get_contents($getImageShowHref[0]);

                //Create Image :
                $fileImgName = "Image" . $codeImg;
                file_put_contents("JSON\Images/$fileName/$fileImgName.png", $img_content);
                $state = $state."B";
            }


            //----------------------------------------ICON
            //Get Icon :

            if ($getIconSrc[0] != null) {
                $ico_content = file_get_contents($getIconSrc[0]);

                //Create Icon :
                $fileIcoName = "Icon" . $codeIco;
                file_put_contents("JSON\Images/$fileName/$fileIcoName.png", $ico_content);
                $state = $state."C";
            }


            //----------------------------------------ImageAll
            //Get+Create All Image
            if ($getImageAll != null) {
                $service->ImageAlls($getImageAll, $code);
                $state = $state."D";
            }

            //----------------------------------------Feature
            //Get+Create Feature
            if ($getFeatureSrc != null) {
                $service->getFeatureS($getFeatureSrc, $code);
                $state = $state."E";
            }

            echo $state;


        }
    }
}