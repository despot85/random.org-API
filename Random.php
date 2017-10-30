<?php

class Random 
{
    const SERVICE_URL = 'https://api.random.org/json-rpc/1/invoke';
    private $apiKey;
    private $min;
    private $max;
    private $n;
    
    private $greske = [];
    
    private $brojevi;
    
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }    
    
    public function ucitajParametre($data)
    {
        if (empty($data['Random'])) {
            return false;
        }
        $obj = $data['Random']; 
        $this->min = isset($obj['min']) && ($obj['min'] === '0' || ! empty($obj['min'])) ? intval($obj['min']) : null;
        $this->max = isset($obj['max']) && ($obj['max'] === '0' || ! empty($obj['max'])) ? intval($obj['max']) : null;
        $this->n   = isset($obj['n']) ? intval($obj['n']) : null;
        return true;
    }
    
    public function getParametar($attr)
    {
        if ($this->$attr === null) {
            return '';
        }
        return $this->$attr;
    }
    
    public function proveriIspravnost()
    {
        if ( ! $this->n || $this->n < 0) {
            $this->greske['n'] = 'N mora biti pozitivni celi broj veći od nule';
        }
        if ($this->max === null) {
            $this->greske['max'] = 'Unesite max vrednost';
        }
        if ($this->min === null) {
            $this->greske['min'] = 'Unesite min vrednost';
        } elseif ($this->max !== null && $this->min >= $this->max) {
            $this->greske['min'] = 'Min vrednost mora biti manja od max';
            $this->greske['max'] = 'Max vrednost mora biti veća od max';            
        }
        return empty($this->greske);
    }
    
    public function getError($attr)
    {
        return empty($this->greske[$attr]) ? false : $this->greske[$attr];
    }
    
    /**
     * Vraca json string
     * @return string
     */
    private function posaljiZahtev() 
    {
        $url = 'URL';
        $zahtev = [
            "jsonrpc" => "2.0",
            "method"  => "generateIntegers",
            "params"  => [
                "apiKey" => $this->apiKey,
                "n"      => $this->n,
                "min"    => $this->min,
                "max"    => $this->max,
                "replacement" => true
            ],
            "id" => 1            
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($zahtev),
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents(self::SERVICE_URL, false, $context);  
        if ($result === false) {
            throw new \Exception('Greška prilikom slanja zateva generatoru slučajnih brojeva');
        }
        return $result;
    }
    
    private function parsirajRezultat($odgovor)
    {
        $asocijativniNiz = true;
        $json = json_decode($odgovor, $asocijativniNiz);
        if ( ! isset($json['result']['random']['data'])) {
            throw new \Exception('Podaci nisu u odgovarajućem formatu');
        }
        $this->brojevi = $json['result']['random']['data'];
    }
    
    /**
     * Za dati niz brojeva vraca niz gde je kljuc jedinstveni broj, 
     * a broj njegovih pojavljivanja u originalnom nizu je vrednost koja se cuva pod kljucem
     * @return array
     */
    public function getUcestalost()
    {
        $ret = [];
        foreach ($this->brojevi as $broj) {
            if ( ! empty($ret[$broj])) {
                $ret[$broj] ++;
            } else {
                $ret[$broj] = 1;
            }
        }
        return $ret;
    }
    
    /**
     * 
     * @return array
     */
    public function getSviBrojeviIUcestalost()
    {
        $ret = [];
        $ucestalost = $this->getUcestalost();
        foreach ($this->brojevi as $broj) {
            $ret[$broj] = $ucestalost[$broj];
        }
        return $ret;
    }
        
    public function ucitaj()
    {
        $odgovor = $this->posaljiZahtev();
        $this->parsirajRezultat($odgovor);
    }
}