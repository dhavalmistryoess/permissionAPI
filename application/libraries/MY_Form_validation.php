<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Form_validation extends CI_Form_validation
{
    function __construct($config = array())
    {
      parent::__construct($config);
    }

    function error_array()
    {
      if (count($this->_error_array) === 0)
        return FALSE;
      else
        return $this->_error_array;
    }
    public function is_checknumber($str)
    {
        if(!parent::decimal($str))
        {
            return (bool) parent::integer($str);
        }

        return TRUE;
    }
    public function compareDate()
    {
        $startDate = strtotime($_POST['ValidFrom']);
        $endDate = strtotime($_POST['ValidTo']);
        
        if ($endDate >= $startDate)
          return True;
        else {
          return False;
        }
    }
    
    public function date_validate($date)
    {
        $getDate = strtotime(date('Y-m-d', strtotime($date)));
        $currentDate = strtotime(date('Y-m-d'));
       
        if ($getDate >= $currentDate)
          return True;
        else {
          return False;
        }
    }
    
    public function compareCertificateDate()
    {
        $startDate = strtotime(str_replace('/', '-', $_POST['ReleaseDate']));
        $endDate = strtotime(str_replace('/', '-', $_POST['ValidUpto']));
        
        if ($endDate >= $startDate){
          return true;
        }
        else {
          return False;
        }
    }
    
    
    public function validateDate($dated,  $format = 'm/d/Y') {
          if($dated == 'NaN/NaN/NaN') { return false; }
          $d = DateTime::createFromFormat($format, $dated);
          return $d && $d->format($format) == $dated;
    }


    public function is_phonenumber($str)
    {
        return (bool)preg_match('/^[~&a-z0-9 _-]+$/i', $str);
     
    }
    public function is_validTime($str)
    {
        return (bool)preg_match('/(?:[01]\d|2[0123]):(?:[012345]\d):(?:[012345]\d)/', $str);
     
    }
}