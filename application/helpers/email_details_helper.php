<?php
if (!function_exists('SendEmail')) {

    function SendEmail($smtpEmail=null, $to=null, $cc=null, $bcc=null, $subject=null, $body=null)
    {
        $CI = & get_instance();
        try{
            $CI->email->from($smtpEmail, 'OpenEyes Technologies Inc.');
            $CI->email->to($to);        
            $CI->email->subject($subject);
            $CI->email->cc($cc);
            $CI->email->bcc($bcc);
            $CI->email->message($body);
            if($CI->email->send())
            {
                $email_log = array(
                    'From' => trim($smtpEmail),
                    'Cc' => trim($cc),
                    'Bcc' => trim($bcc),
                    'To' => trim($to),
                    'Subject' => trim($subject),
                    'EmailBody' => trim($body),
                );  
                $res = $CI->db->insert('tblemaillog',$email_log);   
                if (!empty($db_error) && !empty($db_error['code'])) { 
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    return false; // unreachable return statement !!!
                }
                if($res){
                    return true; 
                } else {
                    return false; 
                }
            }else
            {
                return false;
            }
        }   
        catch(Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

}

if (!function_exists('getSmtpDetails')) {

    function getSmtpDetails()
    {
        $CI = & get_instance();
        try{

                $CI->db->select('Key, Value');
                $names = ['EmailFrom', 'EmailPassword', 'SmtpHost', 'SmtpPort'];
                $CI->db->where_in('Key', $names);
                $smtp1 = $CI->db->get('tblmstconfiguration');
            
                if($smtp1->num_rows() > 0)  {
                $row1 = $smtp1->result_array();
                    $getResultArray = array_column($row1, 'Value', 'Key');
                    $res['smtpEmail'] = $getResultArray['EmailFrom'];
                    $res['smtpPassword'] = $getResultArray['EmailPassword'];
                    $res['smtpHost'] = $getResultArray['SmtpHost'];
                    $res['smtpPort'] = $getResultArray['SmtpPort'];
                if ($res) {
                    return $res;
                } else {
                    return false;
                }
            }
            
            
        }   
        catch(Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }
    
}

if (!function_exists('getEmailDetails')) {

    function getEmailDetails($EmailToken= null,$UserId=null)
    {
        $CI = & get_instance();
        try{        
            $query = $CI->db->query("SELECT et.To,et.Subject,et.EmailBody,et.BccEmail,(SELECT GROUP_CONCAT(UserId SEPARATOR ',') FROM tblusers WHERE RoleId = et.To && ISActive = 1) AS totalTo,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tblusers WHERE RoleId = et.Cc && ISActive = 1) AS totalcc,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tblusers WHERE RoleId = et.Bcc && ISActive = 1) AS totalbcc FROM tblemailtemplate AS et LEFT JOIN tblmsttoken as token ON token.TokenId=et.TokenId WHERE token.TokenName = '".$EmailToken."' && et.IsActive = 1");
            $res=$query->result();
            
            $queryTo = $CI->db->query('SELECT EmailAddress FROM tblusers where UserId = '.$UserId); 
            $rowTo = $queryTo->result();

            $query1 = $CI->db->query('SELECT p.PlaceholderId,p.PlaceholderName,t.TableName,c.ColumnName FROM tblmstemailplaceholder AS p LEFT JOIN tblmsttablecolumn AS c ON c.ColumnId = p.ColumnId LEFT JOIN tblmsttable AS t ON t.TableId = c.TableId WHERE p.IsActive = 1');
            $body = $row->EmailBody;  
            foreach($query1->result() as $row1){			
                $query2 = $CI->db->query('SELECT '.$row1->ColumnName.' AS ColumnName FROM '.$row1->TableName.' AS tn LEFT JOIN tblmstroles AS role ON tn.RoleId = role.RoleId WHERE tn.UserId = '.$UserId);
                $result2 = $query2->result();
                $body = str_replace("{ ".$row1->PlaceholderName." }",$result2[0]->ColumnName,$body);					
            } 
            
            
            if($row->BccEmail!=''){
                $bcc = $row->BccEmail.','.$row->totalbcc;
            } else {
                $bcc = $row->totalbcc;
            }
            $res['EmailBody']=$body;
            $res['Subject']=$row->Subject; 
            $res['Bcc']=$bcc;
            $res['To']=$rowTo[0]->EmailAddress;
            $res['Cc']=$row->totalcc; 
            if($res){
                return $res;
            }
            else{
                return false;
            }
        }   
        catch(Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }
    
}


if (!function_exists('sendEmailFunction')) {

    function sendEmailFunction($EmailToken= null, $UserId=null, $response = null, $userEmail = '', $IsNotification = false)
    {
        $CI = & get_instance();
        try {
            $CI->db->select('Key, Value');
            $names = ['EmailFrom', 'EmailPassword', 'SmtpHost', 'SmtpPort'];
            $CI->db->where_in('Key', $names);
            $smtp1 = $CI->db->get('tblmstconfiguration');

            if ($smtp1->num_rows() > 0) {
                $row1 = $smtp1->result_array();
                $getResultArray = array_column($row1, 'Value', 'Key');
                
               
                $query = $CI->db->query("SELECT et.EmailTemplateId,et.To,et.Subject,et.EmailBody,et.BccEmail,(SELECT GROUP_CONCAT(UserId SEPARATOR ',') FROM tblusers WHERE RoleId = et.To && ISActive = 1) AS totalTo,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tblusers WHERE RoleId = et.Cc && ISActive = 1) AS totalcc,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tblusers WHERE RoleId = et.Bcc && ISActive = 1) AS totalbcc FROM tblemailtemplate AS et LEFT JOIN tblmsttoken as token ON token.TokenId=et.TokenId WHERE token.TokenName = '".$EmailToken."' && et.IsActive = 1");
                $row = $query->row();
                
                if($UserId > 0) {
                    $queryTo = $CI->db->query('SELECT EmailAddress FROM tblusers where UserId = ' . $UserId);
                    $rowTo = $queryTo->row();
                } else {
                    $rowTo = new stdClass();
                    $rowTo->EmailAddress = $userEmail;
                }
              
                $query1 = $CI->db->query('SELECT p.PlaceholderId,p.PlaceholderName,t.TableName,c.ColumnName FROM tblmstemailplaceholder AS p LEFT JOIN tblmsttablecolumn AS c ON c.ColumnId = p.ColumnId LEFT JOIN tblmsttable AS t ON t.TableId = c.TableId WHERE p.IsActive = 1');
                $body = $row->EmailBody;
                if ($UserId > 0) {
                    foreach ($query1->result() as $row1) {
                        $query2 = $CI->db->query('SELECT ' . $row1->ColumnName . ' AS ColumnName FROM ' . $row1->TableName . ' AS tn LEFT JOIN tblmstroles AS role ON tn.RoleId = role.RoleId WHERE tn.UserId = ' . $UserId);
                        $result2 = $query2->result();
                        $body = str_replace("{ " . $row1->PlaceholderName . " }", $result2[0]->ColumnName, $body);
                    }
                }
                if ($row->BccEmail != '') {
                    $bcc = $row->BccEmail . ',' . $row->totalbcc;
                } else {
                    $bcc = $row->totalbcc;
                }
               
                $getFormattedBody = getFormattedBody($response, $body);
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = $getResultArray['SmtpHost'];
                $config['smtp_port'] = $getResultArray['SmtpPort'];
                $config['smtp_user'] = $getResultArray['EmailFrom'];
                $config['smtp_pass'] = $getResultArray['EmailPassword'];
                $config['charset'] = 'utf-8';
                $config['newline'] = "\r\n";
                $config['mailtype'] = 'html';
                $CI->email->initialize($config);
                $CI->email->from($getResultArray['EmailFrom'], 'OpenEyes Technologies Inc.');
                $CI->email->to($rowTo->EmailAddress);
                $CI->email->subject($row->Subject);
                $CI->email->cc($row->totalcc);
                $CI->email->bcc($bcc);
                $CI->email->message($getFormattedBody);
                if ($CI->email->send()) {
                    $email_log = array(
                        'ModuleId' => 2,
                        'EmailTemplateId' => trim($row->EmailTemplateId),
                        'From' => trim($getResultArray['EmailFrom']),
                        'Cc' => trim($row->totalcc),
                        'Bcc' => trim($bcc),
                        'To' => trim($rowTo->EmailAddress),
                        'Subject' => trim($row->Subject),
                        'EmailBody' => trim($body)
                    );
                    $CI->email->clear(TRUE);
                    $res = $CI->db->query('call AddEmailLogs(?,?,?,?,?,?,?,?)', $email_log);
                    if (!empty($db_error) && !empty($db_error['code'])) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                        return false; // unreachable return statement !!!
                    }
                    
                    if($IsNotification){
                        $res3 = InsertNotification($response->RoleId, $UserId, $response->NotificationText, $response->ActionURL, $response->CreatedBy);
                    }
                    if ($res) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }
    
}
?>