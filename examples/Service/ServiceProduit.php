<?php


class ServiceProduit
{
    public function getJSON($fileName)
    {
        $fileName = "JSON/".$fileName;
        $result = json_decode(file_get_contents($fileName), true);


        return $result;
    }

}