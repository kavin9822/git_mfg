<?php

/**
 * Description of SignUpManager
 *
 * @author ycs-gunabalans@yahoo.com
 */

 include_once 'Util/Gen.php'; 
 
class SignUpManager extends Util\Gen {

    protected  $crg;
    protected  $db;
    private $getHeaders;
    private $req_method;

    public function __construct($reg = NULL) {
        $this->crg = $reg;
        $this->db = $this->crg->get('db');
        $this->getHeaders = $this->crg->get('HttpHeaders');
        $this->req_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
        $this->baseUrl = $this->crg->get('appConfig')['host_name'] . '/' . $this->crg->get('base_folder');
    }

///////////////Constructor completed here/////////////////
//////////////////////////////////////////////////////////

    public function init() {
        //check for request method
        if ($this->req_method !== 'POST') {
            return $this->crg->setResponseCode(400, 'Request method should be POST');
        }

        $ctype = trim($this->getHeaders['Content-Type']);
        //check for content type
        if (empty($ctype)) {
            return $this->crg->setResponseCode(400, 'Content type Not set, Request header Content-Type application/x-www-form-urlencoded - Required');
        }

        if (!strstr('application/x-www-form-urlencoded', $ctype)) {
            return $this->crg->setResponseCode(400, 'Request header Content-Type application/x-www-form-urlencoded - Required');
        }

        /*
         * Get login form post data
         */


        $this->HTTPMethodStringFromUrl = isset($this->crg->get('uri_array')[1]) ? $this->crg->get('uri_array')[1] : FALSE;

        switch ($this->HTTPMethodStringFromUrl) {
            case 'resendOtp':
                $this->resendOtp();
                break;
            case 'setPwd':
                $this->handleSetPwd();
                break;
            case 'otp':
                $this->handleOtp();
                break;
            default:
                $this->handleSignUp();
                break;
        }
    }

    //////////////////////////////init complete here///////////////////////////////////////////////////////
    /*
     * You can add custom class method her
     */


    private function resendOtp() {
        $userMob = filter_input(INPUT_POST, 'MobileNo', FILTER_SANITIZE_STRING);

        /*
         * If ther is no mobile number then return
         */
        if (empty($userMob)) {
            return $this->crg->setResponseCode(400, 'Mobile number required');
        }
        
        /*
         * Check for already registered
         */
        if ($this->db->have('user_role', ['UserID' => $userMob])) {
            return $this->crg->setResponseCode(400, 'User already registered');
        }

        /*
         * generate OTP int
         */
        $otpInt = rand(100000, 999999);

        
            $DataToUpdate = [
                'UserOtp' => $otpInt,
                '#AuditDateTime' => "NOW()" // do all time cal in database
            ];
            $wcon = ['MobileNo' => $userMob];

        $desc = "OTP is resend to the registered mobile number and email id";
        $lnk = $this->baseUrl . "/signUp/setPwd";

        /*
         * send otp to registed mobile
         */
        if ($this->smsgateway($userMob, $mesageWithPw)) {
            
         //if message sent then update reg. data in resp tables 
         $regData = $this->db->update('user', $DataToUpdate, $wcon); 
         if ($regData >= 1 && $this->curlManager($userMob, $mesageWithPw, $type)) {
            return $this->crg->setResponseCode(200, $desc, '', $lnk);
          }else{
            return $this->crg->setResponseCode(200, str_replace("and email id","",$desc),'', $lnk);
          }
        } else {
            return $this->crg->setResponseCode(401, 'Sorry, we are unable to deliver message to your mobile no. and email, try again');
        }
    }

///resendOtp

    private function handleSetPwd() {
        $userMob = filter_input(INPUT_POST, 'MobileNo', FILTER_SANITIZE_STRING);
        //check double entry password in the client 
        $userPasswd = filter_input(INPUT_POST, 'PassWord', FILTER_SANITIZE_STRING);
        $userRole = filter_input(INPUT_POST, 'Role', FILTER_SANITIZE_STRING);
        $entityID = filter_input(INPUT_POST, 'EntityID', FILTER_SANITIZE_STRING);
        
        
        if (empty($userMob)) {
            return $this->crg->setResponseCode(400, 'Mobile Number required');
        } elseif (empty($userPasswd)) {
            return $this->crg->setResponseCode(400, 'Password required');
        } elseif (empty($userRole)) {
            return $this->crg->setResponseCode(400, 'Role required');
        } elseif(empty($entityID)){
            return $this->crg->setResponseCode(400, 'Entity required');
        }

        $updatePwSucc = FALSE;

            $dataToUpdate = ['UserPwd' => md5($userPasswd),
                            'entity_ID' => $entityID];
            $updateWc = ['MobileNo' => $userMob];


        ////////////////else update the password///////////////////////////////////
        $regData = $this->db->update('user', $dataToUpdate, $updateWc);

        ///////////////////////////////if password set then/////////////////////
        if ($regData >= 1) {
            $desc = "Password was set and Registration completed successfully";
            $lnk = $this->baseUrl . "/login/" . $userRole;
            /////////////////////////update role in user Role table/////////////////// 
            $urData = [
                'RoleID' => $userRole,
                'UserID' => $userMob,
            ];

            $this->db->insert('user_role', $urData);
            ////////////////////////////////////////////////////////////////////////////
            if ($this->db->has('user_role', ["AND" => $urData])) {
                return $this->crg->setResponseCode(200, $desc, NULL, $lnk);
            } else {
                return $this->crg->setResponseCode(200, $desc . ' But, user role not Set, Contact admin', NULL, $lnk);
            }
        } else {
            $lnk = $this->baseUrl . "signUp";
            return $this->crg->setResponseCode(400, 'Try again', NULL, $lnk);
        }
    }

//handle password

    private function handleOtp() {
        $userMob = filter_input(INPUT_POST, 'MobileNo', FILTER_SANITIZE_STRING);
        $userOTP = filter_input(INPUT_POST, 'OTP', FILTER_SANITIZE_STRING);

        if (empty($userMob)) {
            return $this->crg->setResponseCode(400, 'Mobile Number required');
        } elseif (empty($userOTP)) {
            return $this->crg->setResponseCode(400, 'OTP required');
        }
        
        //$updatePwSucc = FALSE;
        $otpSessDur = $this->crg->get('appConfig')['otpSessionDuration'];
        
            $DataToSel = "*";
            $wcon = ["AND" => [
                    "MobileNo" => $userMob,
                    "UserOtp" => $userOTP,
                    "#AuditDateTime[>]" => "(NOW() - INTERVAL " . $otpSessDur . ' MINUTE)'
            ]];

        /*
         * check OTP and also 
         * check time difference less than $otpSessDur set in the application config
         */
        $chkOtp = $this->db->select('user', $DataToSel, $wcon);
        if (count($chkOtp) >= 1) {
            $lnk = $this->baseUrl . "/signUp/setPwd";
            $desc = "OTP verified successfully and set your password to complete registration process";
            return $this->crg->setResponseCode(200, $desc, NULL, $lnk);
        } else {
            $lnk = $this->baseUrl . "/otp";
            $msg = 'Dear User, Pl. Check OTP or if OTP with more than ' . $otpSessDur . ' minutes then try with resend OTP';
            return $this->crg->setResponseCode(401, $msg, NULL, $lnk);
        }
    }

//handleOtp

    /*
     * handleSignUp
     */

    private function handleSignUp() {
        $userName = filter_input(INPUT_POST, 'UserName', FILTER_SANITIZE_STRING);
        $userMob = filter_input(INPUT_POST, 'MobileNo', FILTER_SANITIZE_STRING);
        $userEm = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
        /*
         * If ther is no mobile number then return
         */
        if (empty($userMob)) {
            return $this->crg->setResponseCode(400, 'Mobile number required');
        }
        // responce data
        $data = [
            "MobileNo" => $userMob
        ];
        
        /*
         * Check for already registered
         */
        if ($this->db->have('user_role', ['UserID' => $userMob])) {
            return $this->crg->setResponseCode(400, 'User already registered');
        }
        
        /*
         * generate OTP int
         */
        $otpInt = rand(100000, 999999);
           
            $DataToInsertAr = [
                'UserName' => $userName,
                'Email' => $userEm,
                'MobileNo' => $userMob,
                'UserOtp' => $otpInt,
                '#AuditDateTime' => "NOW()" // time stamp updated at the time of registration that is sending otp, this can be used to otp expiry purpose
            ];
            

        $desc = "Successful , Registration Partially Competed and OTP is send to the registered mobile no. and email id to complete the registration process";
        $lnk = $this->baseUrl . "/signUp/otp";


        /*
         * send otp to registed mobile
         */
         $mesageWithPw = 'The OTP is ' . $otpInt;
         
         if ($this->smsgateway($userMob, $mesageWithPw)) {
             
        //if message sent then update reg. data in resp tables 
         $regData = $this->db->insert('user', $DataToInsertAr);
         //echo $this->db->last_query();     
             
         $type = "signup";
        
         if ($regData >= 1 && $this->curlManager($userMob, $mesageWithPw, $type)) {
            
            return $this->crg->setResponseCode(200, $desc, $data, $lnk);
            
          }else{
            return $this->crg->setResponseCode(200, str_replace("and email id","",$desc),$data, $lnk);
          }
        } else {
            return $this->crg->setResponseCode(401, 'Sorry, we are unable to deliver message to your mobile no. and email, Signup later');
        }
    }
    
}

/////////End of SignUpManager class///////////////////////