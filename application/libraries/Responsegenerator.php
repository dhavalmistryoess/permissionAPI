<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

class Responsegenerator extends REST_Controller
{
    
     public function __construct() {
         parent::__construct();
         $this->load->library("session");
         
    }
 
    function generateResponse($code, $message, $moduleName = "")
    {
        switch($code) {
            case 404:
                    switch($moduleName) {
                        case "Category":
                            $message = "Category not found";
                            break;
                        default :
                            $message;
                            break;
                    }
                    
            break;
            case 422:
                    switch($moduleName) {
                        case "addfinalsubmit":
                            $message = "Something went wrong";
                            break;
                        default :
                            $message;
                            break;
                    }
                    
            break;
        }
        $this->response([
            'code' => $code,
            'message' => $message
                ], $code);
    }
  
 

}
?>