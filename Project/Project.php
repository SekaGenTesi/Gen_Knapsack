<?php
Class Parameters{ 
    const FILE_NAME = 'komponenPC.txt';
    const COLUMNS = ['item', 'price','jenis'];
    const POPULATION_SIZE = 80;
    const BUDGET =  24000000;
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

Class individu{
    function countNumberOfGen(){
        $catalogue = new Catalogue;
        return count($catalogue -> product()); 
    }

    function createRandomIndividu(){
        for ($i=0;$i<= $this->countNumberOfGen() - 1;$i++){
            $ret[] = rand(0,1);
            
        }
        return $ret; 
        
    }
}

class Population{
        function createRandomPopulation(){
            $individu = new individu;
            for($i = 0;$i <= Parameters::POPULATION_SIZE - 1 ; $i++){ 
               $ret[] =  $individu -> createRandomIndividu(); 
            }
            return $ret; 
            
        }
}

class Fitness{

    function Highprice($listcomp){
        $x=0;
        $max=$listcomp[0];

        for($i=0;$i<sizeof($listcomp)-1;$i++){
           if($max > $listcomp[$x+1]['selectedPrice']){
                $max = $listcomp[$x+1];
           }
        }

        return $max;
    }

    function listbaru($listprocessor,$listmainboard,$listmemori,$listvgacard,$listharddisk){
        
        for($i=0;$i<5;$i++){
            switch($i){
                case 0:
                    $newlist[]=$this->Highprice($listprocessor);
                    break;
                case 1:
                    $newlist[]=$this->Highprice($listmainboard);
                    break;
                case 2:
                    $newlist[]=$this->Highprice($listmemori);
                    break;
                case 3:
                    $newlist[]=$this->Highprice($listvgacard);
                    break;
                case 4:
                    $newlist[]=$this->Highprice($listharddisk);
                    break;    
            }
        }

        return $newlist;
        // foreach($newlist as $new){
        //     echo'<br>';
        //     print_r($new);
        // }
    }

    function split($list){
        
        foreach($list as $listIndividu){
            if(strpos($listIndividu['type'], "processor")!== false){
                $listprocessor[]=[
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
               
            }
            if(strpos($listIndividu['type'], "mainboard")!== false){
                $listmainboard[]=[
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "memori")!== false){
                $listmemori[]=[
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "vgacard")!== false){
                $listvgacard[]=[
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "harddisk")!== false){
                $listharddisk[]=[
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
        }

        
        $listbaru = $this->listbaru($listprocessor,$listmainboard,$listmemori,$listvgacard,$listharddisk);
       
        $list = $listbaru;
        
        return $list;
    }

    function selectingItem($individu){
        $catalogue = new Catalogue;
        foreach($individu as $individuKey => $binaryGen){
            if($binaryGen === 1){
                $ret[] = [
                    'selectedKey' => $individuKey,
                    'selectedPrice' => $catalogue -> product()[$individuKey]['price'],
                    'type' => $catalogue -> product()[$individuKey]['jenis']
                ]; 
            }
            // else if($binaryGen === 0){
            //     $ret[] = [
            //         'NotselectedKey' => $individuKey,
            //         'NotselectedPrice' => $catalogue -> product()[$individuKey]['price'],
            //         'type' => $catalogue -> product()[$individuKey]['jenis']
            //     ]; 
            // }
            
        }
        $ret = $this->split($ret);
        foreach($ret as $new){
            echo'<br>';
            print_r($new);
        }
        return $ret;
    }

    function calculateFitnessValue($individu){
        return array_sum(array_column($this -> selectingItem($individu),'selectedPrice'));
       
    }

    function countSelectedItem($individu){
        return count($this->selectingItem($individu));
    }

    function searchBestIndividu($fits,$maxItem,$numberOfIndividuMaxItem){
        if($numberOfIndividuMaxItem === 1){
            $index = array_search($maxItem, array_column($fits,'numberOfSelectedItem'));
            return $fits[$index];
            echo'<br>';
        }
        else{ 
            foreach($fits as $key => $val){
                if($val['numberOfSelectedItem'] === $maxItem){
                    echo $key.' '.$val['fitnessValue'].'<br>';
                    $ret[] =[
                        'individuKey' => $key,
                        'fitnessValue' => $val['fitnessValue']

                    ];
                }
            }
            if(count(array_unique(array_column($ret, 'fitnessValue'))) === 1){
                $index = rand(0, count($ret) - 1);
            }
            else{
                $max = max(array_column($ret,'fitnessValue'));
                $index = array_search($max,array_column($ret,'fitnessValue'));
            }
            echo '<br>Hasil: ';
            return $ret[$index]; 
        }
    }

    function isFound($fits){

        $countedMaxItem = array_count_values(array_column($fits,'numberOfSelectedItem'));
        print_r($countedMaxItem); 
        echo '<br>';
        $maxItem = max(array_keys($countedMaxItem));
        echo $maxItem;
        echo '<br>';
        echo $countedMaxItem[$maxItem];
        echo '<br>';
        $numberOfIndividuMaxItem = $countedMaxItem[$maxItem];

        $bestFitnessValue = $this -> searchBestIndividu($fits,$maxItem,$numberOfIndividuMaxItem)['fitnessValue'];
        
        echo '<br>Best fitness value: '.$bestFitnessValue;
 
        $residual = Parameters::BUDGET - $bestFitnessValue;
        echo 'Residual: '. $residual;

        if($residual <= Parameters::STOPING_VALUE && $residual > 0){
            return True;
        }
    }

    function isFit($fitnessValue){
        if($fitnessValue <= Parameters::BUDGET){
            return True;
        }
    }


   
    function fitnessEvaluation($population){
        $catalogue = new Catalogue;
        foreach($population as $listOfindividuKey => $listOfIndividu){
            echo 'Individu-'. $listOfindividuKey. '<br>';
            foreach ($listOfIndividu as $individuKey => $binaryGen){
                echo $binaryGen.'&nbsp;&nbsp';
                print_r($catalogue -> product()[$individuKey]);
                echo '<br>';
            }
            //sudah
            echo'<br>';

            //$splitlistindividu = $this->splitIndividu($listOfIndividu);
            $fitnessValue = $this->calculateFitnessValue($listOfIndividu); 
            //$numberOfSelectingItem = $this -> countSelectedItem($listOfIndividu);
            $numberOfSelectingItem = 0;

            
            echo '<br><br>';
            echo 'Max Item: '.$numberOfSelectingItem;
            echo  ' Fitness Value :'. $fitnessValue;

            if($this -> isFit($fitnessValue)){
                echo '(Fit)';
                $fits[] = [
                    'selectedIndividuKey' => $listOfindividuKey,
                    'numberOfSelectedItem' => $numberOfSelectingItem,
                    'fitnessValue' => $fitnessValue
                ];
                print_r($fits); 
            }
            else{
                echo '(Not Fit)';
            }
           
            echo '<p>';
        }

        if($this -> isFound($fits)){
            echo ' Found';
        }
        else{
            echo'>> next generation';
        }
       
    }
}
    

$initialPopulation = new Population; 
$population = $initialPopulation -> createRandomPopulation();

$fitness = new Fitness;
$fitness -> fitnessEvaluation($population);

?>