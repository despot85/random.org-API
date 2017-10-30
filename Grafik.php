<?php

class Grafik
{        
    private static function getBoje($ucestalost, $alfa)
    {
        $max = max($ucestalost);
        $ret = '';
        foreach ($ucestalost as $vrednost) {
            $r = intval(255 * $vrednost/(float)$max);
            $g = 0;
            $b = 255 - $r;
            $ret .= "'rgba($r, $g, $b, $alfa)',";
        }
        return '[' . trim($ret, ',') . ']';
    }
    
    public static function getJs($kontejnerId, $rand)
    {
        $ucestalost = $rand->getUcestalost();
        $labele = json_encode(array_keys($ucestalost));
        $podaci = json_encode(array_values($ucestalost));  
        $pozadina = self::getBoje($ucestalost, '0.2');
        $okvir    = self::getBoje($ucestalost, '1');
        return <<<JS
        $(document).ready(function(){
            var ctx = document.getElementById("$kontejnerId").getContext("2d");
            var data = {S
                labels: $labele,
                datasets: [
                {
                    label: "Učestalost",
                    data: $podaci,                
                    backgroundColor: $pozadina,
                    borderColor: $okvir,
                    borderWidth: 1
                }]
            };
            var options = {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            };    
            var grafik = new Chart(ctx, { type:'bar', data:data, options:options});            
        });
JS;
    }
}

