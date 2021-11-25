<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Crawler</title>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var positif = parseInt(document.getElementById('positif').value);
            var negatif = parseInt(document.getElementById('negatif').value);
            var netral = parseInt(document.getElementById('netral').value);
            var data = google.visualization.arrayToDataTable([
                ['Title', 'Count'],
                ['Positif',     positif],
                ['Negatif',      negatif],
                ['Netral',      netral],
            ]);

            var options = {
                title: 'My Daily Activities',
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>

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
                    <li class="nav-item active">
                        <a class="nav-link font-weight-bold" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="evaluasi.php">Evaluasi</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="menu-content">
            <h3 class="text-center text-dark font-weight-bold mt-4 pb-4">Analisis Tweet dan Analisis Sentimen</h3>

            <div class="search-content pl-5 pr-5 mb-5">
                <form method="GET" action="">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Keyword</span>
                    </div>
                    <input type="text" class="form-control" name="search" placeholder="Masukkan kata kunci..." aria-label="Username" aria-describedby="basic-addon1">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-primary rounded-right">Search</button>
                    </div>
                </div>
            </div>

            <div class="radio-content pl-5 pr-5">
                <div class="input-group mb-3">
                    <div class="input-group-prepend mr-4">
                        <span class="input-group-text rounded" id="basic-addon1">Pilih Metode Similaritas</span>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metode" id="inlineRadio1" value="Overlap" required>
                        <label class="form-check-label" for="inlineRadio1">Overlap</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metode" id="inlineRadio2" value="Asymmetric" required>
                        <label class="form-check-label" for="inlineRadio2">Asymmetric</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metode" id="inlineRadio3" value="Cosine" required>
                        <label class="form-check-label" for="inlineRadio3">Cosine</label>
                    </div>
                </form>
                </div>

                <hr>
            </div>

            <h5 class="text-center text-dark font-weight-bold mt-4 pb-4">Hasil Crawling dan Analisis Sentimen</h5>

            <div id="piechart_3d" style="width: 900px; height: 500px;"></div>
            <?php 
             $curl = curl_init();

             $query = $_GET['search'];    
             $search_param = [$_GET['search']];    
             $query_search = str_replace(" ","-",$query);  
             // echo preg_replace("/[@]/", "", $query);;
 
             curl_setopt_array($curl, array(
                 CURLOPT_URL => 'https://api.twitter.com/2/tweets/search/recent?query='.$query_search,
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_ENCODING => '',
                 CURLOPT_MAXREDIRS => 10,
                 CURLOPT_TIMEOUT => 0,
                 CURLOPT_FOLLOWLOCATION => true,
                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                 CURLOPT_CUSTOMREQUEST => 'GET',
                 CURLOPT_HTTPHEADER => array(
                     'Authorization: Bearer AAAAAAAAAAAAAAAAAAAAADnEVQEAAAAAfETct%2BxTRxF9lSBAjVOEcwinlRU%3DR7jOXW59k0XdzwFcRRKAbIDIpal2Kbp8lf4cuGoG1bEJJYfJNP',
                     'Cookie: _twitter_sess=BAh7CSIKZmxhc2hJQzonQWN0aW9uQ29udHJvbGxlcjo6Rmxhc2g6OkZsYXNo%250ASGFzaHsABjoKQHVzZWR7ADoPY3JlYXRlZF9hdGwrCCmeQ%252Bp8AToMY3NyZl9p%250AZCIlMWUzNGU0YmVmODQyOTU3MDQzYzcyYjZkYzg3ZTQxNDE6B2lkIiUyNTU0%250AOWRiNzkxZDUyNDMwYjg4YTgzY2UzMzE5YjM1YQ%253D%253D--2431f134e55afe115f1682f988261b4fdc7b01a3; guest_id=v1%3A163601275321835451; personalization_id="v1_48RVyazKiYWfuQhrMpK0Hw=="'
                 ),
             ));
 
             $response = curl_exec($curl);
             $result_upd  = json_decode($response, true);
             curl_close($curl);
 
             // echo "<pre>";
             $countResult = count($result_upd["data"]);
             // echo "</pre>";
             $sample_data = [];
             $paramNo=0;
             foreach ($result_upd["data"] as $item) { 
                 error_reporting(0);
 
                 // Filtering Text
                 $pattern_username = '/(@[a-zA-Z0-9_]+)/';
                 $teks_username = preg_replace($pattern_username, ' ', $item["text"]);
 
                 // Match Link
                 $pattern_link = '/(http|https|ftp|ftps):\/\/[a-zA-Z0-9-.]+.[a-zA-Z0-9]+(\/S*)?/';
                 $teks_link = preg_replace($pattern_link, ' ', $teks_username);
 
                 // Match Emoticons
                 $pattern_emoticon = '/[\x{1F600}-\x{1F64F}]/u';
                 $teks_emoticon = preg_replace($pattern_emoticon, '', $teks_link);
 
                 // Match Miscellaneous Symbols and Pictographs
                 $pattern_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
                 $teks_pictographs = preg_replace($pattern_symbols, '', $teks_emoticon);
 
                 // Match Transport And Map Symbols
                 $pattern_transport = '/[\x{1F680}-\x{1F6FF}]/u';
                 $teks_symbols = preg_replace($pattern_transport, '', $teks_pictographs);
 
                 // Match Miscellaneous Symbols
                 $pattern_misc = '/[\x{2600}-\x{26FF}]/u';
                 $teks_miscellaneous = preg_replace($pattern_misc, '', $teks_symbols);
 
                 // Match Dingbats
                 $pattern_dingbats = '/[\x{2700}-\x{27BF}]/u';
                 $teks = preg_replace($pattern_dingbats, '', $teks_miscellaneous);
 
                 $user_del = ltrim($teks, $user[0]);
                 $teks = strtolower(trim($user_del));
 
                 $teks = str_replace("'", " ", $teks);
 
                 $teks = str_replace('   "', " ", $teks);
 
                 $teks = str_replace("-", " ", $teks);
             
                 $teks = str_replace(")", " ", $teks);
             
                 $teks = str_replace("(", " ", $teks);
             
                 $teks = str_replace("\"", " ", $teks);
             
                 $teks = str_replace("/", " ", $teks);
             
                 $teks = str_replace("=", " ", $teks);
             
                 $teks = str_replace(".", " ", $teks);
             
                 $teks = str_replace(",", " ", $teks);
             
                 $teks = str_replace(":", " ", $teks);
             
                 $teks = str_replace(";", " ", $teks);
             
                 $teks = str_replace("!", " ", $teks);
                 
                 $teks = str_replace("?", " ", $teks);
 
                 $teks = str_replace("rt", " ", $teks);
                 
                 $string = trim(preg_replace('/\s\s+/', ' ', $teks));
                 $json_decoded = json_decode($string);
                 array_push($sample_data, $string);
                 $paramNo++;
             }  
                $teks_query = strtolower(trim($query));
                $string_query = trim(preg_replace('/\s\s+/', ' ', $teks_query));
                
                 array_push($sample_data, $string_query);

                require_once __DIR__ . '/vendor/autoload.php';

                use Phpml\FeatureExtraction\TokenCountVectorizer;
                use Phpml\Tokenization\WhiteSpaceTokenizer;
                use Phpml\FeatureExtraction\TfIdfTransformer;
            
                $tf = new TokenCountVectorizer(new WhiteSpaceTokenizer());
                $tf->fit($sample_data);
                $tf->transform($sample_data);
                
                $vocabulary = $tf->getVocabulary();
                $tfidf = new TfIdfTransformer($sample_data);
                $tfidf->transform($sample_data);

                $i=1;
                $array = [];
                foreach ($sample_data as $row) {
                    array_push($array, $row);
                    $i++;
                }
                echo "</table><br>"; 
                array_push($array, $row);

                // echo "<pre>";
                // print_r($array);
                // echo "</pre>";
                
                $countData = count($array[0]);
                $countAllData = count($array); ?>
                    <div class="pl-5 pr-5">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Username</th>
                                <th scope="col">Id Tweets</th>
                                <th scope="col">Tweets</th>
                                <th scope="col">Tweets Preprocessing Text</th>
                                <th scope="col">Nilai Sentimen</th>
                                <th scope="col">Sentimen</th>
                                </tr>
                            </thead>
                            <tbody>

                    <?php $y=1;
                    $positif = 0;
                    $negatif = 0;
                    $netral = 0;
                    foreach ($result_upd["data"] as $item) { 
                        error_reporting(0);

                        // Filtering Text
                        $pattern_username = '/(@[a-zA-Z0-9_]+)/';
                        $teks_username = preg_replace($pattern_username, ' ', $item["text"]);

                        // Match Link
                        $pattern_link = '/(http|https|ftp|ftps):\/\/[a-zA-Z0-9-.]+.[a-zA-Z0-9]+(\/S*)?/';
                        $teks_link = preg_replace($pattern_link, ' ', $teks_username);

                        // Match Emoticons
                        $pattern_emoticon = '/[\x{1F600}-\x{1F64F}]/u';
                        $teks_emoticon = preg_replace($pattern_emoticon, '', $teks_link);

                        // Match Miscellaneous Symbols and Pictographs
                        $pattern_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
                        $teks_pictographs = preg_replace($pattern_symbols, '', $teks_emoticon);

                        // Match Transport And Map Symbols
                        $pattern_transport = '/[\x{1F680}-\x{1F6FF}]/u';
                        $teks_symbols = preg_replace($pattern_transport, '', $teks_pictographs);

                        // Match Miscellaneous Symbols
                        $pattern_misc = '/[\x{2600}-\x{26FF}]/u';
                        $teks_miscellaneous = preg_replace($pattern_misc, '', $teks_symbols);

                        // Match Dingbats
                        $pattern_dingbats = '/[\x{2700}-\x{27BF}]/u';
                        $teks = preg_replace($pattern_dingbats, '', $teks_miscellaneous);

                        $user_del = ltrim($teks, $user[0]);
                        $teks = strtolower(trim($user_del));
                        $teks = str_replace("'", " ", $teks);
                        $teks = str_replace('   "', " ", $teks);
                        $teks = str_replace("-", " ", $teks);
                        $teks = str_replace(")", " ", $teks);
                        $teks = str_replace("(", " ", $teks);
                        $teks = str_replace("\"", " ", $teks);
                        $teks = str_replace("/", " ", $teks);
                        $teks = str_replace("=", " ", $teks);
                        $teks = str_replace(".", " ", $teks);
                        $teks = str_replace(",", " ", $teks);
                        $teks = str_replace(":", " ", $teks);
                        $teks = str_replace(";", " ", $teks);
                        $teks = str_replace("!", " ", $teks);
                        $teks = str_replace("?", " ", $teks);
                        $teks = str_replace("rt", " ", $teks);
                        $string = trim(preg_replace('/\s\s+/', ' ', $teks));

                        $json_decoded = json_decode($string);
                        array_push($array_data, $string);
                        array_push($array_text, $string);

                        // Formula Overlap
                        if($_GET['metode'] == "Overlap"){
                            $resultDQ = 0.0;
                            $resultD = 0.0;
                            $resultQ = 0.0;
                            
                            for($i = 0; $i < $countData-1; $i++)
                            {
                                $resultDQ += $array[$countAllData-1][$i]*$array[$y][$i];
                                $resultD += pow($array[2][$i], 2);
                                $resultQ += pow($array[$countAllData-1][$i], 2);
                            }

                            $resultDQ = $resultDQ;
                            $resultD = $resultD;
                            $resultQ = $resultQ;
                            $result = $resultDQ/min($resultD, $resultQ);
                        }
                        // Formula Asymmetric
                        elseif($_GET['metode'] == "Asymmetric"){
                            $resultDQ = 0;
                            $resultQ = 0;
                            
                            for($i = 0; $i < $countData-1; $i++)
                            {
                                $resultDQ += (min($array[$countAllData-1][$i], $array[$y][$i]));
                                $resultQ += $array[$countAllData-1][$i];
                            }

                            $resultDQ = $resultDQ;
                            $resultQ = $resultQ;
                            $result = round($resultDQ/$resultQ,1);
                        }
                        // Formula Cosine
                        elseif($_GET['metode'] == "Cosine"){
                            $resultDQ = 0;
                            $resultD = 0;
                            $resultQ = 0;
                            
                            for($i = 0; $i < $countData-1; $i++)
                            {
                                $resultDQ += ($array[$y][$i] * $array[$countAllData-1][$i]);
                                $resultD += pow($array[$y][$i], 2);
                                $resultQ += pow($array[$countAllData-1][$i],2);
                            }

                            $resultDQ = $resultDQ;
                            $resultD = sqrt($resultD);
                            $resultQ =  sqrt($resultQ);
                            $result = round($resultDQ/($resultD*$resultQ),1);

                            
                        } ?>
                        <tr>
                            <th scope="row"><?=$y;?></th>
                            <td><?=$user[0]?></td>
                            <td><?=$item["id"]?></td>
                            <td><?=$item["text"]?></td>
                            <td><?=$teks?></td>
                            
                            <?php if($result >= 1){ ?>
                                <td class="text-success text-bold">1</td>
                            <?php }elseif($result < 1 || $result >= 0.5){ ?>
                                <td class="text-danger text-bold">0.5</td>
                            <?php }else{ ?>
                                <td class="text-bold">0</td>
                            <?php }

                            if($result >= 1){ 
                                $positif += 1; ?>
                                <td class="text-success text-bold">Positif</td>
                            <?php }elseif($result < 1 || $result >= 0.5){ 
                                $negatif += 1; ?>
                                <td class="text-danger text-bold">Negatif</td>
                            <?php }else{ 
                                $netral += 1; ?>
                                <td class="text-bold">Netral</td>
                            <?php } ?>
                        </tr>
                        
                    <?php 

                    include 'src/connection/connection.php';

                    // Overlap
                    $resultDQO = 0;
                    $resultDO = 0;
                    $resultQO = 0;
                    for($i = 0; $i < $countData-1; $i++)
                    {
                    $resultDQO += ($array[10][$i]*$array[$y][$i]);
                    $resultDO += pow($array[$y][$i], 2);
                    $resultQO += pow($array[10][$i], 2);
                    }

                    $resultDQO = $resultDQO;
                    $resultDO = $resultDO;
                    $resultQO = $resultQO;
                    $resultO = round($resultDQO/min($resultDO, $resultQO),1);

                    // Asymmetric
                    $resultDQA = 0;
                    $resultQA = 0;
                    
                    for($i = 0; $i < $countData-1; $i++)
                    {
                    $resultDQA += (min($array[10][$i], $array[$y][$i]));
                    $resultQA += $array[10][$i];
                    }

                    $resultDQA = $resultDQA;
                    $resultQA = $resultQA;
                    $resultA = round($resultDQA/$resultQA,1);

                    // Cosine
                    $resultDQC = 0;
                    $resultDC = 0;
                    $resultQC = 0;
                    
                    for($i = 0; $i < $countData-1; $i++)
                    {
                    $resultDQC += ($array[$y][$i] * $array[10][$i]);
                    $resultDC += pow($array[$y][$i], 2);
                    $resultQC += pow($array[10][$i],2);
                    }

                    $resultDQC = $resultDQ;
                    $resultDC = sqrt($resultD);
                    $resultQC =  sqrt($resultQ);
                    $resultC = round($resultDQ/($resultD*$resultQ),1);

                    $ids = $item["id"];

                    // Input to Database
                    $query = "INSERT INTO tb_tweets (id_tweets, id_username, username, tweets, overlap, asymmetric, cosine) 
                    values 
                    (null, '$ids', '','$teks','$resultO', '$resultA', '$resultC')";
                    mysqli_query($connect, $query);
                    $y++;
                    } ?>
                    
                        </tbody>
                    </table>
                    
                    <!-- Parameter value use Pie Chart -->
                    <input type="hidden" value="<?=$positif?>" id="positif">
                    <input type="hidden" value="<?=$negatif?>" id="negatif">
                    <input type="hidden" value="<?=$netral?>" id="netral">
                </div>
            </div>


        </div>
        </div>
    </div>
</body>
</html>