<?php
include_once (__DIR__).'/../../config/config.php';

class TFO_cURL {
    
    // Constants
    private $api_url = API_PATH;
    private $api_key = API_KEY;

    // cURL Parameters
    public $action;         // string - API command. Valid actions are 'lab' and 'creature'
    public $var;            // string - Variable input. Takes lab-name for 'lab', creature-code for 'creature.
    public $output;         // variable - return object from cURL request
    public $error;           // boolean - tracks error

    private function prepare($payload) {
        $handle = curl_init($this->api_url);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        return $handle;
    }

    function execute() {
        $data = array(
            'key' => $this->api_key,
            'action' => $this->action,
            'var' => $this->var
        );
        $payload = json_encode($data);
        $handle = $this->prepare($payload);
        $this->output = curl_exec($handle);
        if (curl_errno($handle)) {
            $this->error = curl_error($handle);
        }
        curl_close($handle);
    }
}