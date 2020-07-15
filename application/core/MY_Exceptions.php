<?php
class MY_Exceptions extends CI_Exceptions
{

    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
       
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        header("HTTP/1.1 404 Internal Server Error");

        echo json_encode(
            array(
                'code' => 404,
                'message' => 'Page not found',
            )
        );
        die;
       
    }
    public function show_404($page = '', $log_error = TRUE) {
        

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        header("HTTP/1.1 404 Internal Server Error");

        echo json_encode(
            array(
                'code' => 404,
                'message' => 'Page not found',
            )
        );
        die;
      
    }
    public function show_exception($exception) {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        header("HTTP/1.1 404 Internal Server Error");

        echo json_encode(
            array(
                'code' => 404,
                'message' => $exception,
            )
        );
        die;
    }
}