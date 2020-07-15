<?php
if (!function_exists('getFormattedBody')) {

    function getFormattedBody($res=null,$body=null)
    {
        $CI = & get_instance();
        try{
            $CurrentYear = date("Y");
            $data = json_decode(json_encode($res), True);
            $Password = (isset($data['Password'])?$data['Password']:'');
            $ForgotUrl = (isset($data['forgotUrl'])?$data['forgotUrl']:'');
            $UserName = (isset($data['UserName'])?$data['UserName']:'');
            $ActionBy = (isset($data['ActionBy'])?$data['ActionBy']:'');
            $CandidateName = (isset($data['CandidateName'])?$data['CandidateName']:'');
            $EmailAddress = (isset($data['EmailAddress'])?$data['EmailAddress']:'');
            $ContactEmail = (isset($data['ContactEmail'])?$data['ContactEmail']:'');
            $ContactPhoneNo = (isset($data['ContactPhoneNo'])?$data['ContactPhoneNo']:'');
            $ContactMessage = (isset($data['ContactMessage'])?$data['ContactMessage']:'');
            $CertificateName = (isset($data['CertificateName'])?$data['CertificateName']:'');
            $AvailablePriorityDate1 = (isset($data['AvailablePriorityDate1'])?$data['AvailablePriorityDate1']:'');
            $AvailablePriorityDate2 = (isset($data['AvailablePriorityDate2'])?$data['AvailablePriorityDate2']:'');
            $AvailablePriorityDate3 = (isset($data['AvailablePriorityDate3'])?$data['AvailablePriorityDate3']:'');
            $AssignDate = (isset($data['AssignDate'])?$data['AssignDate']:'');
            $AssessmentStartTime = (isset($data['StartTime'])?$data['StartTime']:'');
            $AssessmentEndTime = (isset($data['EndTime'])?$data['EndTime']:'');
            $Address = (isset($data['Address'])?$data['Address']:'');
            $text = (isset($data['text'])?$data['text']:'');
            $button_text = (isset($data['button_text'])?$data['button_text']:'');
            $title = (isset($data['title'])?$data['title']:'');
            $Comment = (isset($data['AdminComment'])?$data['AdminComment']:'');
            $CancelComment = (isset($data['CancelComment'])?$data['CancelComment']:'');
            $DocumentVerificationComment = (isset($data['DocumentVerificationComment'])?$data['DocumentVerificationComment']:'');
            $Role = (isset($data['Role'])?$data['Role']:'');
            $CertificateEndDate = (isset($data['CertificateEndDate'])?$data['CertificateEndDate']:'');
            $Documents = (isset($data['Documents'])?$data['Documents']:'');
            $SubjectTitle = (isset($data['SubjectTitle'])?$data['SubjectTitle']:'');
            $errorMessage = (isset($data['error_message'])?$data['error_message']:'');
            
            $body = str_replace("{Password}",$Password,$body);
            $body = str_replace("{logo_url}",''.BASE_URL.'/assets/images/oeti.png',$body);
            $body = str_replace("{key_url}",''.BASE_URL.'/assets/images/users_lock.jpg',$body);
            $body = str_replace("{login_url}",$data['loginUrl'],$body);
            $body = str_replace("{forgot_url}",$ForgotUrl,$body);
            $body = str_replace("{current_year}",$CurrentYear,$body);
            $body = str_replace("{userName}",$UserName,$body);
            $body = str_replace("{CandidateName}",$CandidateName,$body);
            $body = str_replace("{ActionBy}",$ActionBy,$body);
            $body = str_replace("{emailAddress}",$EmailAddress,$body);
            $body = str_replace("{contactEmail}",$ContactEmail,$body);
            $body = str_replace("{contactPhoneNo}",$ContactPhoneNo,$body);
            $body = str_replace("{contactMessage}",$ContactMessage,$body);
            $body = str_replace("{certificateName}",$CertificateName,$body);
            $body = str_replace("{AvailablePriorityDate1}",$AvailablePriorityDate1,$body);
            $body = str_replace("{AvailablePriorityDate2}",$AvailablePriorityDate2,$body);
            $body = str_replace("{AvailablePriorityDate3}",$AvailablePriorityDate3,$body);
            $body = str_replace("{AssignDate}",$AssignDate,$body);
            $body = str_replace("{AssessmentStartTime}",$AssessmentStartTime,$body);
            $body = str_replace("{AssessmentEndTime}",$AssessmentEndTime,$body);
            $body = str_replace("{Address}",$Address,$body);
            $body = str_replace("{text}",$text,$body);
            $body = str_replace("{button_text}",$button_text,$body);
            $body = str_replace("{title}",$title,$body);
            $body = str_replace("{comment}",$Comment,$body);
            $body = str_replace("{CancelComment}",$CancelComment,$body);
            $body = str_replace("{DocumentVerificationComment}",$DocumentVerificationComment,$body);
            $body = str_replace("{Role}",$Role,$body);
            $body = str_replace("{certificateEndDate}",$CertificateEndDate,$body);
            $body = str_replace("{subjectTitle}",$SubjectTitle,$body);
            $body = str_replace("{error_message}",$errorMessage,$body);
            $body = str_replace("{Documents}",$Documents,$body);
            return $body;
        }   
        catch(Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

}

?>