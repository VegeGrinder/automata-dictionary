<?php
    $dictionary = explode(" ",$_POST["dict"]);
    $dfa = [];
    $buttons = "";
    $occurence = array();
    
    //foreach($dictionary as $word)
    for($i=0;$i<count($dictionary);$i++)
    {
        traverseDictionary($dfa,$dfa,$dictionary[$i],"");
    }

    function traverseDictionary($dict,$array,$string,$string2) 
    {
        global $dfa;
        if($string == "")
        {
            $dfa = earlyStop($dict,$string2);
            return null;
        }
        else if($array!=null && array_key_exists($string[0], $array))
        {   
            $array = $array[$string[0]];
            $string2 .= $string[0];
            $string = substr($string,1);
        }
        else
        {
            $dfa = formDictionary($dict,array(),$string,$string2);
            return null;
        }
        
        traverseDictionary($dict,$array,$string,$string2);
    }

    function formDictionary($dict,$array,$string,$string2)
    {
        $letters = str_split($string);
        $array[$letters[count($letters)-1]] = array("#" => []);
        for($i=count($letters)-2;$i>=0;$i--)
        {
            $array = array($letters[$i] => $array);
        }
        $length = strlen($string2);
        $letters2 = str_split($string2);

        for($i=$length-1;$i>=0;$i--)
        {
            $target = [];
            $target = dictArray($dict,$string2);
            $target = array($letters2[$i] => array_merge($target,$array));
            $array = $target;
            $string2 = substr($string2,0,-1);
        }

        $array = array_merge($dict,$array);

        return $array;
    }

    function dictArray($dict,$string2)
    {
        $letters = str_split($string2);
        for($i=0;$i<count($letters);$i++)
        {
            $dict = $dict[$letters[$i]];
        }
        return $dict;
    }

    function earlyStop($dict,$string2)
    {
        $array = [];
        $length = strlen($string2);
        $letters2 = str_split($string2);

        for($i=$length-1;$i>=0;$i--)
        {
            $target = [];
            $target = dictArray($dict,$string2);
            if($i == $length-1)
            {
                $target = array($letters2[$i] => array_merge($target,array("#" => [])));
            }
            else
            {
                $target = array($letters2[$i] => array_merge($target,$array));
            }
            $array = $target;
            $string2 = substr($string2,0,-1);
        }
        return $array;
    }
    
    function printText()
    {
        global $dfa, $buttons, $occurence;
        $text = $_POST["input"];
        $text = str_split($text);
        $clone = $dfa;
        $testing = "";
        $index = 0;

        for($i=0;$i<count($text);$i++)
        {
            if(array_key_exists(strtolower($text[$i]), $clone))
            {
                $testing .= $text[$i];
                $clone = $clone[strtolower($text[$i])];
            }
            else
            {
                if(array_key_exists("#",$clone))
                {
                    $index++;
                    echo "<a class=\"text-primary\" name=\"a".$index."\">".$testing."</a>";
                    $buttons .= "<a class=\"btn btn-primary\" href=\"#a".$index."\">".$testing."</a>";
                    
                    if(array_key_exists(strtolower($testing),$occurence))
                    {
                        $occurence[strtolower($testing)]++;
                    }
                    else
                    {
                        $occurence[strtolower($testing)] = 1;
                    }

                    $testing = "";
                    $clone = $dfa;

                    if(array_key_exists(strtolower($text[$i]),$clone))
                    {
                        $testing .= $text[$i];
                        $clone = $clone[strtolower($text[$i])];
                    }
                    else
                    {
                        echo $text[$i];
                    }
                }
                else
                {
                    echo $testing;
                    $testing = "";
                    $clone = $dfa;

                    if(array_key_exists(strtolower($text[$i]),$clone))
                    {
                        $testing .= $text[$i];
                        $clone = $clone[strtolower($text[$i])];
                    }
                    else
                    {
                        echo $text[$i];
                    }
                }
            }
        }
        //for final element
        if(array_key_exists("#",$clone))
        {
            $index++;
            echo "<a class=\"text-primary\" name=\"a".$index."\">".$testing."</a>";
            $buttons .= "<a class=\"btn btn-primary\" href=\"#a".$index."\">".$testing."</a>";
            
            if(array_key_exists(strtolower($testing),$occurence))
            {
                $occurence[strtolower($testing)]++;
            }
            else
            {
                $occurence[strtolower($testing)] = 1;
            }
        }
        else
        {
            echo $testing;
        }
    }

    function occurenceCount()
    {
        global $occurence;

        foreach($occurence as $key => $value)
        {
            echo "<tr><td>".$key."</td><td>".$value."</td></tr>";
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" type ="text/css" href="bootstrap.css">
        <script src="bootstrap.js"></script>
    </head>

    <body>
        <div class="jumbotron">
            <div class="container">
                <h1 for="input">Text:</h1><br>
                <?php printText(); ?>
            </div class>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <h3>Occurence Sequence</h2>
                    <?php echo $buttons; ?>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <h3>Occurence Count</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">String</th>
                                <th scope="col">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php occurenceCount(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
    echo "Just to show the associative array";
    echo "<pre>";
    var_dump($dfa);
    echo "</pre>";
?>