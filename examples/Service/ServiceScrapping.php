<?php

use Symfony\Component\Panther\DomCrawler\Crawler;

class ServiceScrapping
{
    //----------------------------------------------Get Contents :
    private $crawler;

    public function SetCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function getHref($path)
    {
        $value = $this->crawler->filter($path)->each(function ($node) {
            return $href = $node->attr('href');
        });
        return $value;
    }

    public function getText($path)
    {
        $value = $this->crawler->filter($path)->each(function ($node) {
            return $text = $node->text();
        });
        return $value;
    }

    public function getSrc($path)
    {
        $value = $this->crawler->filter($path)->each(function ($node) {
            return $src = $node->attr('src');
        });
        return $value;
    }

    public function getSrcAlt($path)
    {
        $value = $this->crawler->filter($path)->each(function ($node) {
            $src = $node->attr('src');
            $alt = $node->attr('alt');
            return compact('src', 'alt');
        });
        return $value;
    }

    public function getHrefAlt($path)
    {
        $value = $this->crawler->filter($path)->each(function ($node) {
            $href = $node->attr('href');
            $alt = $node->attr('alt');
            return compact('href', 'alt');
        });
        return $value;
    }

    //----------------------------------------------Create Folder :

    public function createFolder($folderName)
    {
        $currentPath = 'C:\Users\MSI Game\untitled\panther\examples' . '/' . $folderName;
        if (!is_dir($currentPath)) {
            mkdir($currentPath, 0777, true);
        }
    }

    public function getPrice($getTableBody)
    {
        foreach ($getTableBody as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if (isset($value1["reference"])) {
                    $reference = $value1["reference"];
                }
                if (isset($value1["price"])) {
                    $explodedValue = explode("HT", $value1["price"]);
                    $showResult = str_replace("€", ".", $explodedValue[0]);
                    $float = floatval($showResult);
                    $price = round($float, 2);
                }
            }
        }
        return $price;
    }

    public function getTableBodyS()
    {

        $nbrRow = $this->crawler->filter('div.main > table.articles-list > tbody > tr ')->count();

        $Rcount = 1;
        while ($Rcount <= $nbrRow) {
            $getTableBody[] = $this->crawler->filter('div.main > table.articles-list > tbody > tr:nth-child(' . $Rcount . ') > td')->each(function ($node) {
                $text[trim($node->attr('headers'))] = trim($node->text());
                return ($text);
            });
            $Rcount++;
        }
        return $getTableBody;
    }

    public function getTableCatalogueS($getCatalogue)
    {
        foreach ($getCatalogue as $keys => $value) {
            $rTable = explode(":", $value);
            $rTableCatalogue[trim($rTable[0])] = trim($rTable[1]);
        }
        return $rTableCatalogue;
    }

    public function getTableS($getTableHead, $getTableBody)
    {
        foreach ($getTableBody as $key => $val) {
            foreach ($val as $subK => $subval) {
                $getTable[$key][$getTableHead[$subK]] = $subval;
            }
        }
        return $getTable;
    }

    public function getCode($element)
    {
        $elementExplode = explode("-", $element);
        $max = max(array_keys($elementExplode));
        $elementCode = explode(".", $elementExplode[$max]);
        $code = $elementCode[0];
        return $code;
    }

    public function getLast($value)
    {
        $elementExplode = explode("/", $value[0]);
        $max = max(array_keys($elementExplode));
        $finaleValue = explode(".", $elementExplode[$max]);
        $result = $finaleValue[0];
        return $result;
    }

    public function ImageAlls($getImageAll, $code)
    {
        foreach ($getImageAll as $imageAll) {
            $imgAll_content = file_get_contents($imageAll);

            $elementExplode = explode("/", $imageAll);
            $max = max(array_keys($elementExplode));
            $finaleValue = explode(".", $elementExplode[$max]);
            $codeImageAll = $finaleValue[0];

            //Create Icon :
            $fileName = "" . $code;
            $fileImgAllName = "Image" . $codeImageAll;
            file_put_contents("JSON\Images/$fileName/$fileImgAllName.png", $imgAll_content);

        }
    }

    public function getFeatureS($getFeatureHref, $code)
    {
        foreach ($getFeatureHref as $feature) {
            $feature_content = file_get_contents($feature);

            $elementExplode = explode("/", $feature);
            $max = max(array_keys($elementExplode));
            $finaleValue = explode(".", $elementExplode[$max]);
            $codeFeature = $finaleValue[0];

            //Create Icon :
            $fileName = "" . $code;
            $fileFeatureName = "Feature" . $codeFeature;
            file_put_contents("JSON\Images/$fileName/$fileFeatureName.png", $feature_content);

        }
    }
}
