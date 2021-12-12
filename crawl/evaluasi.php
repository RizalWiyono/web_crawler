<?php
    namespace PhpmlExercise\Classification;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>



    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

        <!-- Style -->
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body class="bg-light">
    <div class="container bg-white p-0">
        <div class="ss" style="height: 100%;">
            <nav class="navbar navbar-expand-lg navbar-light bg-light" align="center">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="index.php">Home</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link font-weight-bold" href="#">Evaluasi</a>
                        </li>
                    </ul>
                </div>
            </nav>

            

            <div class="menu-content">
                <h3 class="text-center text-dark font-weight-bold mt-4 pb-4">Analisis Tweet dan Analisis Sentimen</h3>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Overlap</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">Tweets</th>
                                    <th scope="col">Sentimen Original</th>
                                    <th scope="col">Sentimen Sistem</th>
                                    <th scope="col">Valid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $arrayOverlap = [];
                                    $arrayTargetOverlap = [];
                                    $arrayStringOverlap = [];
                                    $no=0;
                                    include 'src/connection/connection.php'; 
                                    $query_data  = mysqli_query($connect, "SELECT * FROM tb_tweets");
                                    while($row = mysqli_fetch_array($query_data)){ 

                                        array_push($arrayOverlap, $row["tweets"]); 
                                        array_push($arrayTargetOverlap, $row["overlap"]);
                                        array_push($arrayStringOverlap, $row["tweets"]);?>
                                    
                                    <?php $no++; } 
                                    require_once __DIR__ . '/vendor/autoload.php';

                                    use Phpml\FeatureExtraction\TokenCountVectorizer;
                                    use Phpml\Tokenization\WhitespaceTokenizer;
                                    use Phpml\CrossValidation\StratifiedRandomSplit;
                                    use Phpml\Dataset\ArrayDataset;

                                    $tf = new TokenCountVectorizer(new WhitespaceTokenizer());
                                    $tf->fit($arrayOverlap);
                                    $tf->transform($arrayOverlap);
                                    $vocabulary = $tf->getVocabulary();

                                    // saya ada disini hari ini
                                    //  uts sih  

                                    // saya ada disini hari ini uts sih

                                    $count = count($arrayOverlap);

                                    $dataset = new ArrayDataset(
                                        $samples = $arrayOverlap,
                                        $targets = $arrayTargetOverlap
                                    );

                                    // [training] => Array
                                    //      [0] => Dsini
                                    // [testing] => Array
                                    //      [0] => Dsini
                                    // [target] => Array
                                    //      [0] => 1.5                                   
                                    $datasets = new ArrayDataset(
                                        $samples = $arrayStringOverlap,
                                        $targets = $arrayTargetOverlap
                                    );

                                    // Pembagian Data yang ingin DImasukkan ke Data Training maupun Data Testing
                                    $dataset = new StratifiedRandomSplit($dataset, 0.2, 1234);
                                    $datasets = new StratifiedRandomSplit($datasets, 0.2, 1234);

                                    $xTrainData = $dataset->getTrainSamples();
                                    $yTrainData = $dataset->getTrainLabels();

                                    $xTestData = $dataset->getTestSamples();
                                    $yTestData = $dataset->getTestLabels();

                                    use Phpml\Preprocessing\LabelEncoder;

                                    function label_encode($xData){
                                        $xDataProcessed = [];
                                        $colNum = count($xData[0]);
                                        for($i = 0;$i < $colNum;$i++){
                                            $colData = array_column($xData, $i);
                                            $labelEncoder = new LabelEncoder();
                                            $target = [];
                                            $labelEncoder->fit($colData, $target);
                                            $labels = $labelEncoder->classes();
                                            for($j = 0;$j < count($xData);$j++){
                                                $xDataProcessed[$j][$i] = array_search($xData[$j][$i], $labels);
                                            }
                                        }
                                        return $xDataProcessed;
                                    }

                                    $xTrainEncoded = label_encode($xTrainData);
                                    $xTestEncoded = label_encode($xTestData);

                                    // Data Training
                                    use Phpml\Classification\DecisionTree;
                                    $model = new DecisionTree();
                                    $model->train($xTrainEncoded, $yTrainData);
                                    
                                    // Data Testing
                                    $prediction = [];
                                    for($i = 0;$i < count($xTestEncoded);$i++){
                                        $prediction[$i] = $model->predict($xTestEncoded[$i]);
                                    }

                                    $newData = (array)$datasets;
                                    $newDatas = [];
                                    foreach($newData as $item){
                                        array_push($newDatas, $item);
                                    }
                                    // echo "<pre>";
                                    // print_r($newDatas);
                                    // echo "</pre>";

                                    $no=0;
                                    foreach($newDatas[1] as $row){
                                        error_reporting(0);
                                        $param_tweets = $newDatas[3][$no];
                                        include 'src/connection/connection.php';
                                        $sql_param  = mysqli_query($connect, "SELECT * FROM tb_tweets WHERE tweets='$row' LIMIT 1");
                                        while($rows = mysqli_fetch_array($sql_param)){
                                    ?>
                                    <tr>
                                        <th scope="row"><?=$row?></th>
                                        <td><?=$param_tweets?></td>
                                        <td><?=$rows["overlap"]?></td>
                                        <?php
                                        if($param_tweets == $rows["overlap"]){ 
                                            $accuration_overlap += 1;
                                            ?>
                                            <td>V</td>
                                        <?php }else{ ?>
                                            <td>X</td>
                                        <?php } ?>
                                    </tr>
                                    <?php } $no++; } ?>
                                </tbody>
                            </table>
                            <h5>Jumlah Data Testing: <?=count($newDatas[1])?></h5>
                            <h5>Jumlah Data Valid: <?=$accuration_overlap?></h5>
                            <h5>Jumlah Data Tidak Valid: <?=count($newDatas[1])-$accuration_overlap?></h5>
                            <h5>Akurasi: <?=$accuration_overlap/count($newDatas[1])*100?>%</h5>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <h5>Asymmetric</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">Tweets</th>
                                    <th scope="col">Sentimen Original</th>
                                    <th scope="col">Sentimen Sistem</th>
                                    <th scope="col">Valid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $arrayAsymmetric = [];
                                    $arrayTargetAsymmetric = [];
                                    $arrayStringAsymmetric = [];
                                    $no=0;
                                    include 'src/connection/connection.php'; 
                                    $query_data  = mysqli_query($connect, "SELECT * FROM tb_tweets");
                                    while($row = mysqli_fetch_array($query_data)){ 
                                    // array_push($array, ["uye" => $row["tweets"], "value" => $row["overlap"]]);
                                    array_push($arrayAsymmetric, $row["tweets"]); 
                                    array_push($arrayTargetAsymmetric, $row["asymmetric"]);
                                    array_push($arrayStringAsymmetric, $row["tweets"],); ?>
                                    
                                    <?php $no++; } 
                                    require_once __DIR__ . '/vendor/autoload.php';

                                    $tf->fit($arrayAsymmetric);
                                    $tf->transform($arrayAsymmetric);
                                    $vocabulary = $tf->getVocabulary();

                                    $count = count($arrayAsymmetric);

                                    $dataset = new ArrayDataset(
                                        $samples = $arrayAsymmetric,
                                        $targets = $arrayTargetAsymmetric
                                    );

                                    $datasets = new ArrayDataset(
                                        $samples = $arrayStringAsymmetric,
                                        $targets = $arrayTargetAsymmetric
                                    );

                                    $dataset = new StratifiedRandomSplit($dataset, 0.2, 1234);
                                    $datasets = new StratifiedRandomSplit($datasets, 0.2, 1234);
                                    $xTrainData = $dataset->getTrainSamples();
                                    $yTrainData = $dataset->getTrainLabels();
                                    $xTestData = $dataset->getTestSamples();
                                    $yTestData = $dataset->getTestLabels();

                                    function label_encode_asymmetric($xData){
                                        $xDataProcessed = [];
                                        $colNum = count($xData[0]);
                                        for($i = 0;$i < $colNum;$i++){
                                            $colData = array_column($xData, $i);
                                            $labelEncoder = new LabelEncoder();
                                            $target = [];
                                            $labelEncoder->fit($colData, $target);
                                            $labels = $labelEncoder->classes();
                                            for($j = 0;$j < count($xData);$j++){
                                                $xDataProcessed[$j][$i] = array_search($xData[$j][$i], $labels);
                                            }
                                        }
                                        return $xDataProcessed;
                                    }
                                    $xTrainEncoded = label_encode_asymmetric($xTrainData);
                                    $xTestEncoded = label_encode_asymmetric($xTestData);

                                    
                                    $model = new DecisionTree();
                                    $model->train($xTrainEncoded, $yTrainData);
                                    
                                    $prediction = [];
                                    for($i = 0;$i < count($xTestEncoded);$i++){
                                        $prediction[$i] = $model->predict($xTestEncoded[$i]);
                                    }

                                    $newData = (array)$datasets;
                                    $newDatas = [];
                                    foreach($newData as $item){
                                        array_push($newDatas, $item);
                                    }
                                    // echo "<pre>";
                                    // print_r($newDatas);
                                    // echo "</pre>";

                                    $no=0;
                                    foreach($newDatas[1] as $row){
                                        $param_tweets = $newDatas[3][$no];
                                        include 'src/connection/connection.php';
                                        $sql_param  = mysqli_query($connect, "SELECT * FROM tb_tweets WHERE tweets='$row' LIMIT 1");
                                        while($rows = mysqli_fetch_array($sql_param)){
                                    ?>
                                    <tr>
                                        <th scope="row"><?=$row?></th>
                                        <?php
                                        // if($value >= 1){
                                        //     $value = "Positif";
                                        // }elseif($value < 1 || $value >= 0.5){
                                        //     $value = "Negatif";
                                        // }else{
                                        //     $value = "Netral";
                                        // }

                                        // if($rows["asymmetric"] >= 1){
                                        //     $param = "Positif";
                                        // }elseif($rows["asymmetric"] < 1 || $rows["asymmetric"] >= 0.5){
                                        //     $param = "Negatif";
                                        // }else{
                                        //     $param = "Netral";
                                        // }
                                        ?>
                                        <td><?=$param_tweets?></td>
                                        <td><?=$rows["asymmetric"]?></td>
                                        <?php
                                        if($param_tweets == $rows["asymmetric"]){ 
                                            $accuration_asymmetric += 1;
                                            ?>
                                            <td>V</td>
                                        <?php }else{ ?>
                                            <td>X</td>
                                        <?php } ?>
                                    </tr>
                                    <?php } $no++; } ?>
                                </tbody>
                            </table>
                            <h5>Jumlah Data Testing: <?=count($newDatas[1])?></h5>
                            <h5>Jumlah Data Valid: <?=$accuration_asymmetric?></h5>
                            <h5>Jumlah Data Tidak Valid: <?=count($newDatas[1])-$accuration_asymmetric?></h5>
                            <h5>Akurasi: <?=$accuration_asymmetric/count($newDatas[1])*100?>%</h5>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <h5>Jaccrad</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">Tweets</th>
                                    <th scope="col">Sentimen Original</th>
                                    <th scope="col">Sentimen Sistem</th>
                                    <th scope="col">Valid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $arrayJaccrad = [];
                                    $arrayTargetJaccrad = [];
                                    $arrayStringJaccrad = [];
                                    $no=0;
                                    include 'src/connection/connection.php'; 
                                    $query_data  = mysqli_query($connect, "SELECT * FROM tb_tweets");
                                    while($row = mysqli_fetch_array($query_data)){ 
                                    // array_push($array, ["uye" => $row["tweets"], "value" => $row["overlap"]]);
                                    array_push($arrayJaccrad, $row["tweets"]); 
                                    array_push($arrayTargetJaccrad, $row["jaccard"]);
                                    array_push($arrayStringJaccrad, $row["tweets"],); ?>
                                    
                                    <?php $no++; } 
                                    require_once __DIR__ . '/vendor/autoload.php';

                                    $tf->fit($arrayJaccrad);
                                    $tf->transform($arrayJaccrad);
                                    $vocabulary = $tf->getVocabulary();

                                    $count = count($arrayJaccrad);

                                    $dataset = new ArrayDataset(
                                        $samples = $arrayJaccrad,
                                        $targets = $arrayTargetJaccrad
                                    );

                                    $datasets = new ArrayDataset(
                                        $samples = $arrayStringJaccrad,
                                        $targets = $arrayTargetJaccrad
                                    );

                                    $dataset = new StratifiedRandomSplit($dataset, 0.2, 1234);
                                    $datasets = new StratifiedRandomSplit($datasets, 0.2, 1234);
                                    $xTrainData = $dataset->getTrainSamples();
                                    $yTrainData = $dataset->getTrainLabels();
                                    $xTestData = $dataset->getTestSamples();
                                    $yTestData = $dataset->getTestLabels();

                                    function label_encode_jaccard($xData){
                                        $xDataProcessed = [];
                                        $colNum = count($xData[0]);
                                        for($i = 0;$i < $colNum;$i++){
                                            $colData = array_column($xData, $i);
                                            $labelEncoder = new LabelEncoder();
                                            $target = [];
                                            $labelEncoder->fit($colData, $target);
                                            $labels = $labelEncoder->classes();
                                            for($j = 0;$j < count($xData);$j++){
                                                $xDataProcessed[$j][$i] = array_search($xData[$j][$i], $labels);
                                            }
                                        }
                                        return $xDataProcessed;
                                    }
                                    $xTrainEncoded = label_encode_jaccard($xTrainData);
                                    $xTestEncoded = label_encode_jaccard($xTestData);

                                    
                                    $model = new DecisionTree();
                                    $model->train($xTrainEncoded, $yTrainData);
                                    
                                    $prediction = [];
                                    for($i = 0;$i < count($xTestEncoded);$i++){
                                        $prediction[$i] = $model->predict($xTestEncoded[$i]);
                                    }

                                    $newData = (array)$datasets;
                                    $newDatas = [];
                                    foreach($newData as $item){
                                        array_push($newDatas, $item);
                                    }
                                    // echo "<pre>";
                                    // print_r($newDatas);
                                    // echo "</pre>";

                                    $no=0;
                                    foreach($newDatas[1] as $row){
                                        $param_tweets = $newDatas[3][$no];

                                        if($param_tweets >= 1){
                                            $value = 1;
                                        }elseif($param_tweets < 1 && $param_tweets >= 0.5){
                                            $value = 0.5;
                                        }else{
                                            $value = 0;
                                        }

                                        include 'src/connection/connection.php';
                                        $sql_param  = mysqli_query($connect, "SELECT * FROM tb_tweets WHERE tweets='$row' LIMIT 1");
                                        while($rows = mysqli_fetch_array($sql_param)){
                                    ?>
                                    <tr>
                                        <th scope="row"><?=$row?></th>
                                         <?php
                                        // if($value >= 1){
                                        //     $value = "Positif";
                                        // }elseif($value < 1 || $value >= 0.5){
                                        //     $value = "Negatif";
                                        // }else{
                                        //     $value = "Netral";
                                        // }

                                        // if($rows["Jaccrad"] >= 1){
                                        //     $param = "Positif";
                                        // }elseif($rows["Jaccrad"] < 1 || $rows["Jaccrad"] >= 0.5){
                                        //     $param = "Negatif";
                                        // }else{
                                        //     $param = "Netral";
                                        // }
                                        ?> 
                                        <td><?=$value?></td>
                                        <td><?=$rows["jaccard"]?></td>
                                        <?php
                                        if($param_tweets == $rows["jaccard"]){ 
                                            $accuration_jaccard += 1;
                                            ?>
                                            <td>V</td>
                                        <?php }else{ ?>
                                            <td>X</td>
                                        <?php } ?>
                                    </tr>
                                    <?php } $no++; } ?>
                                </tbody>
                            </table>
                            <h5>Jumlah Data Testing: <?=count($newDatas[1])?></h5>
                            <h5>Jumlah Data Valid: <?=$accuration_jaccard?></h5>
                            <h5>Jumlah Data Tidak Valid: <?=count($newDatas[1])-$accuration_jaccard?></h5>
                            <h5>Akurasi: <?=$accuration_jaccard/count($newDatas[1])*100?>%</h5>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>