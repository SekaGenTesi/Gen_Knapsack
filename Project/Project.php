<?php
Class Parameters{ 
    const FILE_NAME = 'komponenPC.txt';
    const COLUMNS = ['item', 'price','jenis'];
    const POPULATION_SIZE = 5;
    const BUDGET =  26000000;
    const STOPING_VALUE = 30000;
    const CROSSOVERRATE = 0.8;
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
            $ret[] = 0;
            
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
           if($max['selectedPrice'] < $listcomp[$x+1]['selectedPrice']){
                $max = $listcomp[$x+1];
           }
        }

        return $max;
    }

    function listbaru($listprocessor,$listmainboard,$listmemori,$listvgacard,$listharddisk){
        for($i=0;$i<5;$i++){
            switch($i){
                case 0:
                    $newlist[]=$listprocessor[array_rand($listprocessor)];
                    //$newlist[]=$this->Highprice($listprocessor);
                    break;
                case 1:
                    $newlist[]=$listmainboard[array_rand($listmainboard)];
                    //$newlist[]=$this->Highprice($listmainboard);
                    break;
                case 2:
                    $newlist[]=$listmemori[array_rand($listmemori)];
                    //$newlist[]=$this->Highprice($listmemori);
                    break;
                case 3:
                    $newlist[]=$listvgacard[array_rand($listvgacard)];
                    //$newlist[]=$this->Highprice($listvgacard);
                    break;
                case 4:
                    $newlist[]=$listharddisk[array_rand($listharddisk)];
                    //$newlist[]=$this->Highprice($listharddisk);
                    break;    
            }
        }
        foreach($newlist as $key => $v){
            $newlist[$key]['indexBinary']=1;
        }
        return $newlist;
    }

    function split($list){
        
        foreach($list as $listIndividu){
            if(strpos($listIndividu['type'], "processor")!== false){
                $listprocessor[]=[
                    'indexBinary'=> $listIndividu['indexBinary'],
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
               
            }
            if(strpos($listIndividu['type'], "mainboard")!== false){
                $listmainboard[]=[
                    'indexBinary'=> $listIndividu['indexBinary'],
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "memori")!== false){
                $listmemori[]=[
                    'indexBinary'=> $listIndividu['indexBinary'],
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "vgacard")!== false){
                $listvgacard[]=[
                    'indexBinary'=> $listIndividu['indexBinary'],
                    'selectedKey' => $listIndividu['selectedKey'],
                    'selectedPrice' => $listIndividu['selectedPrice'],
                    'type' => $listIndividu['type']
                ];
            }
            if(strpos($listIndividu['type'], "harddisk")!== false){
                $listharddisk[]=[
                    'indexBinary'=> $listIndividu['indexBinary'],
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
            //print_r($binaryGen);
            $ret[] = [
                'indexBinary'=> $binaryGen,
                'selectedKey' => $individuKey,
                'selectedPrice' => $catalogue -> product()[$individuKey]['price'],
                'type' => $catalogue -> product()[$individuKey]['jenis']
            ]; 
            // else if($binaryGen === 0){
            //     $ret[] = [
            //         'NotselectedKey' => $individuKey,
            //         'NotselectedPrice' => $catalogue -> product()[$individuKey]['price'],
            //         'type' => $catalogue -> product()[$individuKey]['jenis']
            //     ]; 
            // }
            
        }
        
        $ret = $this->split($ret);
        // foreach($ret as $new){
        //     echo'<br>';
        //     print_r($new);
        // }
        return $ret;
    }

    function listIndividuBaru($individu){
        return $this -> selectingItem($individu);
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

    function createUpdatePopulation($population,$listNew){
        $catalogue = new Catalogue;
        
        $ret = $population;
        echo '<br>';
        echo '<br>';        
        print_r(count($listNew));
        echo '<br>';
        echo '<br>';    
        for($x=0;$x<count($ret);$x++){
            for($y=0;$y<count($ret[$x]);$y++){
                foreach($listNew as $list){
                    if($y == $list['selectedKey']){
                        $ret[$x][$y] = 1;
                    }
                }
            }
            
        }
        return $ret;
        
        
       
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
            $listNew = $this -> listIndividuBaru($listOfIndividu);
            $fitnessValue = $this->calculateFitnessValue($listOfIndividu); 
            $newPopulation= $this->createUpdatePopulation($population,$listNew);
            //print_r($newPopulation);
            // foreach($listNew as $individu){
            //     echo'<br>';
            //     print_r($individu);
            // }
            //exit();
            //$numberOfSelectingItem = $this -> countSelectedItem($listOfIndividu);
            $numberOfSelectingItem = 5;

            
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

        // print_r($listNew);
        return $newPopulation;
    }
}

class Crossover{
    public $newPopulation;
    
    function __construct($newPopulation){
        $this -> newPopulation = $newPopulation;
    }

    function randomZerotoOne(){
        return (float) rand() / (float) getrandmax();
    }
 
    function generateCrossover(){
        for($i = 0;$i<= Parameters::POPULATION_SIZE-1;$i++){
            $randomZerotoOne = $this -> randomZerotoOne();
            if($randomZerotoOne < Parameters::CROSSOVERRATE){ 
                $parents[$i] = $randomZerotoOne;

            }
        }
        foreach (array_keys($parents) as $key) {
            foreach (array_keys($parents) as $subkey) {
                if($key !== $subkey){
                    $ret[] = [$key,$subkey];
                }
                
            }
            array_shift($parents);
        }
        return $ret; 
    }

    function offspring($parents1,$parents2,$cutPointIndex,$offspring){
        $lengthOfgen = new Individu;

        if($offspring === 1){
            for ($i=0;$i<=$lengthOfgen->countNumberOfGen()-1;$i++){
                if($i <= $cutPointIndex){
                    $ret[] = $parents1[$i];
                }
                if($i > $cutPointIndex){
                    $ret[] = $parents2[$i];
                }
            }
             
        }

        if($offspring === 2){
            for ($i=0;$i<=$lengthOfgen->countNumberOfGen()-1;$i++){
                if($i <= $cutPointIndex){
                    $ret[] = $parents2[$i];
                }
                if($i > $cutPointIndex){
                    $ret[] = $parents1[$i];
                }
            }
           
        }
        return $ret;
    }

    function cutPointRandom(){
        $lengthOfgen = new Individu; 
        return rand(0,$lengthOfgen->countNumberOfGen()-1);
    }

    function crossover(){
        $cutPointIndex = $this->cutPointRandom();
        echo"<br> Cut Point Index: ";
        echo $cutPointIndex;
        foreach($this->generateCrossover() as $listCrossover){
            $parents1 = $this -> newPopulation[$listCrossover[0]];
            $parents2 = $this -> newPopulation[$listCrossover[1]];
            echo"<br><br>";
            echo"Parents: <br>";
        
            foreach($parents1 as $gen){
                echo $gen;
            }
            echo'><';
            foreach($parents2 as $gen){
                echo $gen;
            }
            echo"<br>";
            echo"Offspring Index: <br >";
            $offspring1 = $this->offspring($parents1,$parents2,$cutPointIndex,1);
            $offspring2 = $this->offspring($parents1,$parents2,$cutPointIndex,2);
            foreach($offspring1 as $gen){
                echo $gen;
            }
            echo'><';
            foreach($offspring2  as $gen){

                echo $gen;
            }
        }
        
    } 

}


class Randomizer{
    static function getRandomIndexOfGen(){
        return rand(0,(new Individu())->countNumberOfGen()-1);
    }

    static function getRandomIndexOfIndividu(){
        return rand(0,Parameters::POPULATION_SIZE - 1);
    }
}

class Mutation{
    
    function __construct($population){
        $this->population = $population;
    }

    function calculateMutationRate(){
       
        return 0.2;
    }

    function calculateNumOfMutation(){
        return round($this -> calculateMutationRate() * Parameters::POPULATION_SIZE);
    }

    function isMutation(){ 
        if($this->calculateNumOfMutation()>0){ 
            return TRUE;
        }
    }

    function generateMutation($valueOfGen){
        if($valueOfGen===0){ 
            return 1;
        }
        else{
            return 0;
        }
    }

    function mutation(){
        if($this->isMutation()){ 

            for($i=0 ; $i <= $this->calculateNumOfMutation()-1;$i++){
                
                $indexOfIndividu = Randomizer::getRandomIndexOfIndividu();
                $indexofGen = Randomizer::getRandomIndexOfGen(); 
                $selectedindividu = $this->population[$indexOfIndividu]; 


                echo"<br> Individu ke-";
                print_r($indexOfIndividu);
                echo"<br> Before Mutation: <br>";
                print_r($selectedindividu);

                echo"<br> Letak Gen yang dimutasi <br>";
                print_r($indexofGen);

                $valueOfGen = $selectedindividu[$indexofGen];
                $mutatedGen = $this->generateMutation($valueOfGen);
                $selectedindividu[$indexofGen] = $mutatedGen;

                echo"<br> After Mutation: <br>";
                print_r($selectedindividu);
                echo"<br>";

                $ret[] = $selectedindividu;
            }
            return $ret;
        }

    }

}

$initialPopulation = new Population; 
$population = $initialPopulation -> createRandomPopulation();


print_r($population);
$Fitness = new Fitness;
$newPopulation = $Fitness -> fitnessEvaluation($population);

// foreach($newPopulation as $individukey => $listOfIndividu){
//     print_r($listOfIndividu);
//     echo'<br>';
//     echo'<br>';
//     echo'<br>';
// }
print_r($newPopulation);
$crossover = new Crossover($newPopulation); 
$crossoverOffspring= $crossover->crossover();

echo'Crossover Offsrping: <br>';
print_r($crossoverOffspring);

echo"<p></p>";

//(new Mutation($population))->mutation();
$mutation = new Mutation($newPopulation);
if($mutation->mutation()){

    $mutationOffSprings = $mutation->mutation();
    echo '<br><br>Mutation offspring <br>';
    print_r($mutationOffSprings);
    echo"<p></p>";
    foreach($mutationOffSprings as $mutationOffSprings){
        $crossoverOffspring[] = $mutationOffSprings;
    }
}

echo 'Mutation Offsprings <br>';
print_r($crossoverOffspring);
?>