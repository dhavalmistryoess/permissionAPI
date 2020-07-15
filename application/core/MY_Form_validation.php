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

//    function error_array()
//    {
//      if (count($this->_error_array) === 0)
//        return FALSE;
//      else
//        return $this->_error_array;
//    }
}