<?php
/************************************************************************************** 
 * Handler Object for Managing cURL Requests
 * Primary Key: Composite(uuid, code)
 * 
 * See: https://docs.google.com/document/d/1tRmDw40_VF42uucAZXwYXFfK3qbxM4jaK4YLAjSYFCE/edit
 * for i/o details.
 * 
 * Description:
 * This is a simple little helper object for managing cURL requrests to TFO's API. 
 * $this->action and $this->var map to the corresponding inputs requested by the API; 
 * $this->output holds return objects/messages (if any), and $this->error is tripped to 
 * True in case a cURL error occurs.
 * 
 * Methods:
 * ->execute() - Execute the programmed cURL request (based on $action and $var)
 *                  Requires: $this->action, $this->var
 **************************************************************************************/
include_once (__DIR__).'/../../config/config.php';
class TFO_cURL {
    
    // Constants
    private $api_url = API_PATH;
    private $api_key = API_KEY;

    // cURL Parameters
    public $action;         // string - API command. Valid actions are 'lab' and 'creature'
    public $var;            // string - Variable input. Takes lab-name for 'lab', creature-code for 'creature.
    public $output;         // variable - return object from cURL request
    public $error;          // boolean - tracks error

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