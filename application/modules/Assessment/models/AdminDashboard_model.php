<?php

class AdminDashboard_model extends CI_Model {
    
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    public function getDetails() {
        try {
            $Admin_Data = array();
            $result = $this->db->query('select IFNULL(SUM(uo.TotalAmount), 0) as withoutProctorAmount from tbluserorder uo left join tblusercertificates uc on uo.UserCertificateId = uc.UserCertificateId left join tblmstcertificates c on uc.CertificateId = c.CertificateId where c.HasProctor = 0
                ');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            
            $Admin_Data['withoutProctorAmount'] = $result->row()->withoutProctorAmount;
            
            $result1 = $this->db->query('select IFNULL(SUM(uo.TotalAmount), 0) as withProctorAmount from tbluserorder uo left join tblusercertificates uc on uo.UserCertificateId = uc.UserCertificateId left join tblmstcertificates c on uc.CertificateId = c.CertificateId where c.HasProctor = 1
                ');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['withProctorAmount'] = $result1->row()->withProctorAmount;
            $Admin_Data['totalAmount'] = $Admin_Data['withoutProctorAmount'] + $Admin_Data['withProctorAmount'];

            $result3 = $this->db->query('select count(ua.UserAssessmentId) withoutProctorAssessment from tbluserassessment ua left join tblusercertificates uc on ua.UserCertificateId = uc.UserCertificateId left join tblmstcertificates c on uc.CertificateId = c.CertificateId where c.HasProctor = 0
                ');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['withoutProctorAssessment'] = $result3->row()->withoutProctorAssessment;

            $result4 = $this->db->query('select count(ua.UserAssessmentId) withProctorAssessment from tbluserassessment ua left join tblusercertificates uc on ua.UserCertificateId = uc.UserCertificateId left join tblmstcertificates c on uc.CertificateId = c.CertificateId where c.HasProctor = 1
                ');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['withProctorAssessment'] = $result4->row()->withProctorAssessment;
            $Admin_Data['totalAssessment'] = $Admin_Data['withoutProctorAssessment'] + $Admin_Data['withProctorAssessment'];

            $result5 = $this->db->query('select count(u.UserId) totalUsers from tblusers u where u.IsActive = 1
                ');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['totalUsers'] = $result5->row()->totalUsers;

            $result6 = $this->db->query('select count(c.CertificateId) totalCertificates from tblmstcertificates c where c.IsActive = 1
                ');
           $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['totalCertificates'] = $result6->row()->totalCertificates;

            $result7 = $this->db->query('SELECT sah.ScheduleAssessmentHistoryId,sah.AvailablePriorityDate1,sah.AvailablePriorityDate2,AvailablePriorityDate3,u.UserId,ad.Address1,ad.Address2,ad.Address3,ad.City,
                CASE
                WHEN sah.AvailablePriorityDate1 >= sah.AvailablePriorityDate2 AND sah.AvailablePriorityDate1 >= sah.AvailablePriorityDate3 THEN sah.AvailablePriorityDate1
                WHEN sah.AvailablePriorityDate2 >= sah.AvailablePriorityDate1 AND sah.AvailablePriorityDate2 >= sah.AvailablePriorityDate3 THEN sah.AvailablePriorityDate2
                WHEN sah.AvailablePriorityDate3 >= sah.AvailablePriorityDate1 AND sah.AvailablePriorityDate3 >= sah.AvailablePriorityDate2 THEN sah.AvailablePriorityDate3
                ELSE                                        sah.AvailablePriorityDate1
                END AS MostRecentDate,
                    CONCAT(u.FirstName," ",u.LastName) UserName,u.EmailAddress,c.CertificateName from tblscheduleassessmenthistory sah left join tblmstcertificates c on c.CertificateId = sah.CertificateId left join tblusers u on u.UserId = sah.CandidateId left join tbladdresses ad on ad.ReferenceId = u.UserId and ad.ReferenceTable = "tblusers" and ad.AddressTypeId = 36
                     WHERE sah.ScheduleStatusId = 15 and (u.RoleId = 3 or u.RoleId = 4) having date(MostRecentDate) > CURDATE() order by  MostRecentDate  asc');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            // $res = array(); 
            // foreach($result7->result() as $row) {
            //     if(date("Y-m-d")<$row->MostRecentDate){
            //         array_push($res,$row);
            //     }
            // }
            $Admin_Data['assignData'] = $result7->result();

            $data = array(
                'Active' => 1,
                'Flag' => 0
            );

            $resul8 = $this->db->query('call getAllPendingVerificationUserList(?,?)', $data);
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
               if (!empty($db_error) && $db_error['message'] != "") {
                   throw new Exception($db_error['message']);
               }
            $Admin_Data['pendingVerificationRequest'] = $resul8->result();

            
            $result9 = $this->db->query('call getProctoringDetails(?)', 0);
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['todaysExamination'] = $result9->result();

            $result10 = $this->db->query('select u.*,r.RoleName,c.CertificateId from tblusers u left join tblmstroles r on u.RoleId = r.RoleId left join tblusercertificates uc on uc.UserId = u.UserId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where u.IsActive = 1 and u.RoleId != 1 and c.HasProctor = 1 group by u.UserId order by u.UserId desc limit 5');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['withProctorRegisteredUsers'] = $result10->result();

            $result11 = $this->db->query('select u.*,r.RoleName,c.CertificateId from tblusers u left join tblmstroles r on u.RoleId = r.RoleId left join tblusercertificates uc on uc.UserId = u.UserId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where u.IsActive = 1 and u.RoleId != 1 and c.HasProctor = 0 group by u.UserId order by u.UserId desc limit 5');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['withoutProctorRegisteredUsers'] = $result11->result();

            $result12 = $this->db->query("select YEAR(uo.PaymentDate) years from tbluserorder uo where uo.PaymentStatusId = 49 group by years");
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $Admin_Data['paymentyear'] = array();
            if(!empty($result12->result())){
                foreach ($result12->result() as $year) {
                array_push($Admin_Data['paymentyear'], (int) $year->years);
                $result13 = $this->db->query("select sum(uo.TotalAmount) value,MONTHNAME(uo.PaymentDate) month,MONTH(uo.PaymentDate) monthNumber,YEAR(uo.PaymentDate) year from tbluserorder uo left join tblusercertificates uc on uc.UserCertificateId = uo.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where uo.PaymentStatusId = 49 and YEAR(uo.PaymentDate) = " . $year->years . " and QUARTER(uo.PaymentDate) and c.HasProctor = 0 group by monthNumber");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data = array();
                $data['firstQuarter'] = array();
                $data['secondQuarter'] = array();
                $data['thirdQuarter'] = array();
                $data['forthQuarter'] = array();
                foreach ($result13->result() as $row) {
                    if ($row->monthNumber == 1 or $row->monthNumber == 2 or $row->monthNumber == 3) {
                        array_push($data['firstQuarter'], $row);
                    } else if ($row->monthNumber == 4 or $row->monthNumber == 5 or $row->monthNumber == 6) {
                        array_push($data['secondQuarter'], $row);
                    } else if ($row->monthNumber == 7 or $row->monthNumber == 8 or $row->monthNumber == 9) {
                        array_push($data['thirdQuarter'], $row);
                    } else {
                        array_push($data['forthQuarter'], $row);
                    }
                }
                $Admin_Data['WithoutProctorMonthlyPayment'][$year->years] = $data;

                $result14 = $this->db->query("select sum(uo.TotalAmount) value,MONTHNAME(uo.PaymentDate) month,MONTH(uo.PaymentDate) monthNumber,YEAR(uo.PaymentDate) year from tbluserorder uo left join tblusercertificates uc on uc.UserCertificateId = uo.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where uo.PaymentStatusId = 49 and YEAR(uo.PaymentDate) = " . $year->years . " and QUARTER(uo.PaymentDate) and c.HasProctor = 1 group by monthNumber");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data1 = array();
                $data1['firstQuarter'] = array();
                $data1['secondQuarter'] = array();
                $data1['thirdQuarter'] = array();
                $data1['forthQuarter'] = array();
                foreach ($result14->result() as $row1) {
                    if ($row1->monthNumber == 1 or $row1->monthNumber == 2 or $row1->monthNumber == 3) {
                        array_push($data1['firstQuarter'], $row1);
                    } else if ($row1->monthNumber == 4 or $row1->monthNumber == 5 or $row1->monthNumber == 6) {
                        array_push($data1['secondQuarter'], $row1);
                    } else if ($row1->monthNumber == 7 or $row1->monthNumber == 8 or $row1->monthNumber == 9) {
                        array_push($data1['thirdQuarter'], $row1);
                    } else {
                        array_push($data1['forthQuarter'], $row1);
                    }
                }
                $Admin_Data['WithProctorMonthlyPayment'][$year->years] = $data1;
            }
            }else{
                 array_push($Admin_Data['paymentyear'],date('Y'));
            }

            $result15 = $this->db->query("select YEAR(ua.AssessmentEndDate) years from tbluserassessment ua where ua.ResultStatus is not null group by years");
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $Admin_Data['assessmentyear'] = array();
             if(!empty($result15->result())){
            foreach ($result15->result() as $year) {
                array_push($Admin_Data['assessmentyear'], (int) $year->years);
                $result16 = $this->db->query("select count(ua.UserAssessmentId) value,MONTHNAME(ua.AssessmentEndDate) month,MONTH(ua.AssessmentEndDate) monthNumber,YEAR(ua.AssessmentEndDate) year from tbluserassessment ua left join tblusercertificates uc on uc.UserCertificateId = ua.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where ua.ResultStatus is not null and YEAR(ua.AssessmentEndDate) = " . $year->years . " and QUARTER(ua.AssessmentEndDate) and c.HasProctor = 0 group by monthNumber");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data = array();
                $data['firstQuarter'] = array();
                $data['secondQuarter'] = array();
                $data['thirdQuarter'] = array();
                $data['forthQuarter'] = array();
                foreach ($result16->result() as $row) {
                    if ($row->monthNumber == 1 or $row->monthNumber == 2 or $row->monthNumber == 3) {
                        array_push($data['firstQuarter'], $row);
                    } else if ($row->monthNumber == 4 or $row->monthNumber == 5 or $row->monthNumber == 6) {
                        array_push($data['secondQuarter'], $row);
                    } else if ($row->monthNumber == 7 or $row->monthNumber == 8 or $row->monthNumber == 9) {
                        array_push($data['thirdQuarter'], $row);
                    } else {
                        array_push($data['forthQuarter'], $row);
                    }
                }
                $Admin_Data['WithoutProctorAssessment'][$year->years] = $data;

                $result17 = $this->db->query("select count(ua.UserAssessmentId) value,MONTHNAME(ua.AssessmentEndDate) month,MONTH(ua.AssessmentEndDate) monthNumber,YEAR(ua.AssessmentEndDate) year from tbluserassessment ua left join tblusercertificates uc on uc.UserCertificateId = ua.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where ua.ResultStatus is not null and YEAR(ua.AssessmentEndDate) = " . $year->years . " and QUARTER(ua.AssessmentEndDate) and c.HasProctor = 1 group by monthNumber");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data1 = array();
                $data1['firstQuarter'] = array();
                $data1['secondQuarter'] = array();
                $data1['thirdQuarter'] = array();
                $data1['forthQuarter'] = array();
                foreach ($result17->result() as $row1) {
                    if ($row1->monthNumber == 1 or $row1->monthNumber == 2 or $row1->monthNumber == 3) {
                        array_push($data['firstQuarter'], $row1);
                    } else if ($row1->monthNumber == 4 or $row1->monthNumber == 5 or $row1->monthNumber == 6) {
                        array_push($data1['secondQuarter'], $row1);
                    } else if ($row1->monthNumber == 7 or $row1->monthNumber == 8 or $row1->monthNumber == 9) {
                        array_push($data1['thirdQuarter'], $row1);
                    } else {
                        array_push($data1['forthQuarter'], $row1);
                    }
                }
                $Admin_Data['WithProctorAssessment'][$year->years] = $data1;
            }

            foreach ($result15->result() as $year) {
                $result19 = $this->db->query("select GROUP_CONCAT(c.CertificateId),count(c.CertificateId) count,c.CertificateName,MONTHNAME(ua.AssessmentEndDate) month,MONTH(ua.AssessmentEndDate) monthNumber,YEAR(ua.AssessmentEndDate) year from tbluserassessment ua left join tblusercertificates uc on uc.UserCertificateId = ua.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where ua.ResultStatus is not null and YEAR(ua.AssessmentEndDate) = " . $year->years . " and QUARTER(ua.AssessmentEndDate) and c.HasProctor = 0 group by c.CertificateId order by count desc limit 5");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data = array();
                $data['firstQuarter'] = array();
                $data['secondQuarter'] = array();
                $data['thirdQuarter'] = array();
                $data['forthQuarter'] = array();
                foreach ($result19->result() as $row) {
                    if ($row->monthNumber == 1 or $row->monthNumber == 2 or $row->monthNumber == 3) {
                        array_push($data['firstQuarter'], $row);
                    } else if ($row->monthNumber == 4 or $row->monthNumber == 5 or $row->monthNumber == 6) {
                        array_push($data['secondQuarter'], $row);
                    } else if ($row->monthNumber == 7 or $row->monthNumber == 8 or $row->monthNumber == 9) {
                        array_push($data['thirdQuarter'], $row);
                    } else {
                        array_push($data['forthQuarter'], $row);
                    }
                }
                $Admin_Data['WithoutProctorSellingCertificates'][$year->years] = $data;

                $result20 = $this->db->query("select GROUP_CONCAT(c.CertificateId),count(c.CertificateId) count,c.CertificateName,MONTHNAME(ua.AssessmentEndDate) month,MONTH(ua.AssessmentEndDate) monthNumber,YEAR(ua.AssessmentEndDate) year from tbluserassessment ua left join tblusercertificates uc on uc.UserCertificateId = ua.UserCertificateId left join tblmstcertificates c on c.CertificateId = uc.CertificateId where ua.ResultStatus is not null and YEAR(ua.AssessmentEndDate) = " . $year->years . " and QUARTER(ua.AssessmentEndDate) and c.HasProctor = 1 group by c.CertificateId order by count desc limit 5");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data1 = array();
                $data1['firstQuarter'] = array();
                $data1['secondQuarter'] = array();
                $data1['thirdQuarter'] = array();
                $data1['forthQuarter'] = array();
                foreach ($result20->result() as $row1) {
                    if ($row1->monthNumber == 1 or $row1->monthNumber == 2 or $row1->monthNumber == 3) {
                        array_push($data['firstQuarter'], $row1);
                    } else if ($row1->monthNumber == 4 or $row1->monthNumber == 5 or $row1->monthNumber == 6) {
                        array_push($data1['secondQuarter'], $row1);
                    } else if ($row1->monthNumber == 7 or $row1->monthNumber == 8 or $row1->monthNumber == 9) {
                        array_push($data1['thirdQuarter'], $row1);
                    } else {
                        array_push($data1['forthQuarter'], $row1);
                    }
                }
                $Admin_Data['WithProctorSellingCertificates'][$year->years] = $data1;
            }
             }
             else{
                 array_push($Admin_Data['assessmentyear'],date('Y'));
            }
            $this->responseArr['data'] = $Admin_Data;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    public function getProctoringDetails() {
        try {
            $Admin_Data = array();
            $result = $this->db->query('call getProctoringDetails(?)', 1);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            if ($result->result()) {
                $this->responseArr['data'] = $result->result();
                return $this->responseArr;
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    public function getFilterDefaultData() {
        try {
            $res = array();
            $result1 = $this->db->query('select a.*,s.StateName,c.CountryName from tbladdresses a left join tblmststate s on s.StateId = a.StateId left join tblmstcountry c on a.CountryId = c.CountryId where a.AddressTypeId = 37 and a.ReferenceTable = "tblscheduleassessment" group by a.City');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res1 = array();
            if ($result1->result()) {
                $res1 = $result1->result();
            }

            $result2 = $this->db->query('SELECT u.UserId,CONCAT(u.FirstName," ",u.LastName) as Name FROM tblscheduleassessment as sa left join tblscheduleassessmenthistory sah on sah.ScheduleAssessmentHistoryId = sa.ScheduleAssessmentHistoryId left join tblusers u on u.UserId = sah.CandidateId group by sah.CandidateId');
            $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
            $res2 = array();
            if ($result2->result()) {
                $res2 = $result2->result();
            }

            $result3 = $this->db->query('call GetUserByRoleId(?)', 2);

            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res3 = array();
            if ($result3->result()) {
                $res3 = $result3->result();
            }

            $res['Addresses'] = $res1;
            $res['Candidates'] = $res2;
            $res['Proctors'] = $res3;
            
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    public function filterProctorDetails($post_Data) {
        $AssignDate = $post_Data['AssignDate'];
        $Location = implode('","', $post_Data['Location']);
        //$UserId = implode(",",$post_Data['UserId']);
        $ProctorId = $post_Data['ProctorId'];
        $shiftFilters = $post_Data['shiftFilters'];
        //$dateFilters = [1,2,3];

        try {
            if ((isset($AssignDate) && !empty($AssignDate)) || (isset($Location) && !empty($Location)) || (isset($ProctorId) && !empty($ProctorId)) || (isset($shiftFilters) && !empty($shiftFilters))) {

                $query = 'select sa.*,MIN(sa.StartTime) AS min_start,GROUP_CONCAT(sa.ScheduleAssessmentHistoryId),count(*)
                candidates,CONCAT(u.FirstName," ",u.LastName) ProctorName,u.PhoneNumber,a.Address1,a.Address2,a.Address3,a.City,s.StateName,co.CountryName from tblscheduleassessment sa left join tblusers u on sa.ProctorId = u.UserId left join tbladdresses a on sa.AddressId = a.AddressId left join tblmststate s on a.StateId=s.StateId left join tblmstcountry co on co.CountryId = a.CountryId where sa.ScheduleAssessmentStatus!=56';
                if (isset($ProctorId) && !empty($ProctorId)) {
                    $query .= ' AND sa.ProctorId = ' . $ProctorId;
                }
                if (isset($Location) && !empty($Location)) {
                    $query .= ' AND a.City IN("' . $Location . '")';
                }
                if ((isset($AssignDate) && !empty($AssignDate))) {
                    $query .= ' AND sa.AssignDate = "' . $AssignDate . '"';
                }

                if (!empty($shiftFilters)) {
                    $query .= ' AND (';
                    foreach ($shiftFilters as $key => $shiftFilter) {
                        $date = new \DateTime($shiftFilter);
                        $shift = $date->format('H:i:s');
                        if ($key != 0) {
                            $query .= ' or ';
                        }
                        if ($shift == '17:00:00') {
                            $query .= ' (sa.StartTime < "' . $shift . '" OR sa.EndTime < "' . $shift . '") and sa.EndTime >= "12:00:00" ';
                        } else if ($shift == '20:00:00') {
                            $query .= ' (sa.StartTime < "' . $shift . '" OR sa.EndTime < "' . $shift . '") and sa.EndTime >= "17:00:00" ';
                        } else {
                            $query .= ' sa.StartTime < "' . $shift . '" OR sa.EndTime < "' . $shift . '" ';
                        }
                    }
                    $query .= ' ) ';
                }
                $query .= ' group by sa.StartTime,sa.EndTime,sa.AddressId,sa.ProctorId order by sa.AssignDate desc,min_start';
                //echo $query;die();
                $result = $this->db->query($query);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data = array();
                if ($result->result()) {
                    foreach ($result->result() as $row1) {
                        $certificate_param = array(
                            'UserCertificateId' => $row1->UserCertificateId,
                            'CertificateId' => $row1->CertificateId,
                            'CertificateFor' => $row1->CertificateFor
                        );
                        $result2 = $this->db->query('call GetDocumentsByUserCertificateId(?,?,?)', $certificate_param);
                        mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $OptionalDocuments = array();
                        $MandatoryDocuments = array();
                        foreach ($result2->result() as $row2) {
                            if ($row2->IsMandatory == 0) {
                                array_push($OptionalDocuments, $row2);
                            } else {
                                array_push($MandatoryDocuments, $row2);
                            }
                        }
                        $row1->OptionalDocuments = $OptionalDocuments;
                        $row1->MandatoryDocuments = $MandatoryDocuments;
                        array_push($data, $row1);
                    }
                }
                
                $this->responseArr['data'] = $data;
                return $this->responseArr;
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

}

?>