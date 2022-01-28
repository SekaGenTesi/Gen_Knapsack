<?php

Class Parameters{ 
    const FILE_NAME = 'komponenPC.txt';
    const COLUMNS = ['item', 'price','jenis'];
    const POPULATION_SIZE = 5;
    const BUDGET =  26000000;
    const STOPING_VALUE = 30000;
}

class Catalogue{
    function createProductColumn($listOfRawProduct){ 
        foreach(array_keys($listOfRawProduct) as $listOfRawProductKey){
            $listOfRawProduct[Parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProduct[$listOfRawProductKey]);
        }
        return $listOfRawProduct;
    }
    function product(){
         $collectionOfListProduct = [];

        $raw_data = file(Parameters::FILE_NAME);
        foreach ($raw_data as $listOfRawProduct){
            $collectionOfListProduct[] = $this -> createProductColumn(explode(",",$listOfRawProduct));
        }

        return $collectionOfListProduct;
    }
}

$catalogue = new Catalogue;
//print_r($catalogue->product()[0]);
$listProcessor =  array_column($catalogue->product(),'jenis');
foreach($listProcessor as $listIndividu){
    if(strpos($listIndividu['type'], "processor")!== false){
        $listprocessor[]=[
            'selectedKey' => $listIndividu['selectedKey'],
            'selectedPrice' => $listIndividu['selectedPrice'],
            'type' => $listIndividu['type']
        ];
       
    }
}

print_r($listProcessor[array_rand($listProcessor)]);
