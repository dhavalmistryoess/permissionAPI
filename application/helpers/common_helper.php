<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('checkParamter')) {

    function checkParamter($inputParameter = array(), $requiredParameter = array()) {
        try {
            $notValidParams = array();
            foreach ($inputParameter as $key => $paramater) {
                if (!in_array($key, $requiredParameter)) {
                    $notValidParams[$key] = $key . " Invalid Parameter";
                }
            }

            return $notValidParams;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

}




/* * *** Array sort function **** */
if (!function_exists('array_sort')) {

    function array_sort($array, $on, $order = SORT_ASC) {
        try {
            $array = json_decode(json_encode($array), true);
            $new_array = array();
            $sortable_array = array();
            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == $on) {
                                $sortable_array[$k] = $v2;
                            }
                        }
                    } else {
                        $sortable_array[$k] = $v;
                    }
                }
                switch ($order) {
                    case SORT_ASC:
                        asort($sortable_array, SORT_NATURAL | SORT_FLAG_CASE);
                        break;
                    case SORT_DESC:
                        arsort($sortable_array, SORT_NATURAL | SORT_FLAG_CASE);
                        break;
                }

                foreach ($sortable_array as $k => $v) {
                    $new_array[$k] = $array[$k];
                }
            }

            return $new_array;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

}



/* * *** Generate Pagination response **** */
if (!function_exists('generatePagination')) {
    function generatePagination($responseArr = array(), $limit, $page, $searchCondition, $sortOrder, $searchColumn= array()) {
        try {
            $returnArr =  $resultArr = array();
            if($searchCondition['searchQuery'] != "" && !empty($searchColumn)) {
                $searchData = preg_quote(strtolower(trim($searchCondition['searchQuery'])), '~'); // don't forget to quote input string!
                foreach($searchColumn as $columnName) {
                     $resultArr += preg_grep('~' . $searchData . '~', array_map('strtolower', (array_column($responseArr, $columnName))));
                }
                $responseArr = array_intersect_key($responseArr, array_flip(array_keys($resultArr)));
            }
           
            $returnArr['totalRecord'] = $total = count($responseArr); 
            $totalPages = ceil($total / $limit);
            if (is_array($sortOrder)) {
                foreach ($sortOrder as $key => $val) {
                    if(isset($val['dir'])) {
                        $responseArr = array_sort($responseArr, $val['field'], ($val['dir'] == 'desc') ? SORT_DESC : SORT_ASC);
                    }
                }
            }
            $page = max($page, 1);
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $limit;
            if ($offset < 0) {
                $offset = 0;
            }
       
            $returnArr['result'] = array_slice($responseArr, $offset, $limit);
            return $returnArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }
}

/* * *** getCacheData response **** */
if (!function_exists('getCacheData')) {
    function getCacheData($responseArr = array(), $searchColumn, $searchCondition) {
        try {
            $returnArr =  $resultArr = array();
            if($searchCondition != "" && !empty($searchColumn)) {
                $searchData = preg_quote(strtolower(trim($searchCondition)), '~'); // don't forget to quote input string!
                $resultArr = preg_grep('~' . $searchData . '~', array_map('strtolower', (array_column($responseArr, $searchColumn))));
                $responseArr = array_intersect_key($responseArr, array_flip(array_keys($resultArr)));
            }
            $returnArr['status'] = "success";
            $returnArr['data'] = $responseArr;
            return $returnArr;
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $returnArr['status'] = "exception";
            $returnArr['message'] = $e->getMessage();
            return $returnArr;
        }
    }
}

if (!function_exists('validatePostData')) {
    function validatePostData($parameters, $post, $beautifyParamaters = array())
    {
     
        $CI = &get_instance();
        $CI->load->library('form_validation');
        $CI->form_validation->set_rules($parameters);
        if ($CI->form_validation->run() == FALSE) {
            return array(
                'code' => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $CI->form_validation->error_array()
            );
        } 
        
        return true;
        
    }
}
if (!function_exists('nestedLowercase')) {

    function nestedLowercase($value) {
        if (is_array($value)) {
            return array_map('nestedLowercase', $value);
        }
        return strtolower($value);
    }

}

if (!function_exists('generateResponse')) {
    
    function generateResponse($code, $message, $moduleName = "") {
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
        }
        
        
       
    }
}





