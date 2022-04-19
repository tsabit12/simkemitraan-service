<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Register extends REST_Controller{
     public function __construct(){
		parent::__construct();
          $this->load->model('model_register');
     }

     public function index_post(){
          $response['status']      = false;
          $response['message']     = "Internal server error";
          $response['code']        = 500;

          $data = $this->post();

          if(!isset($data['nik'])){
               $response['message'] = "Nik is required";
               $response['code'] = 202;
          }else{
               $config = array(
                    array('field' => 'nik', 'label' => 'Nik', 'rules' => 'required|max_length[16]|min_length[16]|is_unique[t_mitra.no_ktp]'),
                    array('field' => 'fullname', 'label' => 'Fullname', 'rules' => 'required|min_length[3]|max_length[50]'),
                    array('field' => 'oranger', 'label' => 'Oranger', 'rules' => 'required|callback_validateoranger[]'),
                    array('field' => 'gender', 'label' => 'Gender', 'rules' => 'required|callback_validategender[]'),
                    array('field' => 'birthplace', 'label' => 'Birthplace', 'rules' => 'required|max_length[50]'),
                    array('field' => 'birthday', 'label' => 'Birthday', 'rules' => 'required|callback_validatedate[]'),
                    array('field' => 'religion', 'label' => 'Religion', 'rules' => 'required|callback_validatereligion[]'),
                    array('field' => 'phone', 'label' => 'Phone', 'rules' => 'required|integer|max_length[13]'),
                    array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email'),
                    array('field' => 'nikaddress', 'label' => 'Nik Address', 'rules' => 'required|max_length[250]'),
                    array('field' => 'kprk', 'label' => 'Kprk', 'rules' => 'required|callback_validatekprk[]'),
                    array('field' => 'status', 'label' => 'Status', 'rules' => 'required|callback_validatestatus[]'),
                    array('field' => 'domisiliaddress', 'label' => 'Address domisili', 'rules' => 'required|max_length[250]')
               );
               $this->form_validation->set_data($data);
               $this->form_validation->set_rules($config);
               $this->form_validation->set_message('is_unique', 'The {field} field is already taken.');

               if($this->form_validation->run() === FALSE){
                    $msg_arr  = $this->form_validation->error_array();
                    $keys     = array_keys($msg_arr); 
                    $response['message'] = $msg_arr[$keys[0]];
                    $response['code'] = 201;
               }else{
                    if(!isset($data['berkas'])){
                         $response['message'] = "The Berkas field is required.";
                         $response['code'] = 203;
                    }else{
                         $isvalidberkas = $this->cekberkas($data['berkas']);
                         if($isvalidberkas){
                              $add = $this->model_register->add($data);
                              if($add['success']){
                                   $response['status']      = true;
                                   $response['message']     = "Data saved successfully";
                                   $response['transref'] = $add['info'];
                                   $response['code'] = 200;
                              }else{
                                   $response['message'] = "Register failed, please try again later";
                                   $response['code'] = 204;
                              }
                         }else{
                              $response['message'] = "The Berkas field is not valid.";
                              $response['code'] = 205;
                         }
                    }
               }
          }

          $this->response($response, $response['code']);
     }

     public function validateoranger($oranger){
          if($oranger){
               $validateoranger = $this->model_register->cekoranger($oranger);
               if($validateoranger['isvalid']){
                    return true;
               }else{
                    $this->form_validation->set_message('validateoranger', 'The {field} field is not found.');
                    return false;
               }
          }
     }

     public function validategender($value){
          if($value){
               if($value == 'Pria' || $value == 'Wanita'){
                    return true;
               }else{
                    $this->form_validation->set_message('validategender', 'The {field} is not valid.');
                    return false;
               }
          }
     }

     public function validatedate($value){
          if($value){
               if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)){
                    $this->form_validation->set_message('validatedate', "The {field} field is not valid.");
                    return false;
               }else{
                    return true;
               }
          }
     }

     public function validatereligion($value){
          if($value){
               $list = array(
                    ' ', //dont know why first element doesnt show in array search, so just add space
                    'Islam',
                    'Kristen Protestan',
                    'Katolik',
                    'Hindu',
                    'Buddha',
                    'Kong Hu Cu',
                    'Kepercayaan Lain'
               );

               if(array_search($value, $list)) {
                    return true;
               }else{
                    $this->form_validation->set_message('validatereligion', "The {field} field is not valid.");
                    return false;
               }
          }
     }

     public function validatekprk($value){
          if($value){
               $validatekprk = $this->model_register->cekkprk($value);
               if($validatekprk['isvalid']){
                    return true;
               }else{
                    $this->form_validation->set_message('validatekprk', "The {field} field is not found.");
                    return false;
               }
          }
     }

     public function validatestatus($value){
          if($value){
               if($value == 'Kawin' || $value == 'Belum Kawin'){
                    return true;
               }else{
                    $this->form_validation->set_message('validatestatus', "The {field} field is not valid.");
                    return false;
               }
          }
     }

     public function validateberkas($value){
          if($value){
               if(is_array($value)){
                    if(isset($value['ijazah'])){
                         return true;
                    }else{
                         $this->form_validation->set_message('validateberkas', "The {field} field is not valid.");
                         return false;     
                    }
               }else{
                    $this->form_validation->set_message('validateberkas', "The {field} field must an array.");
                    return false;
               }
          }else{
               $this->form_validation->set_message('validateberkas', "The {field} field must an object $value.");
               return false;
          }
     }

     private function cekberkas($berkas){
          $result = false;
          if(is_array($berkas)){
               if( isset($berkas['ijazah']) && isset($berkas['ktp']) && isset($berkas['sim']) && isset($berkas['skck'])){
                    $result = true;
               }
          }

          return $result;
     }

     public function notification_post(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";

          $data = $this->post();
          if(!isset($data['niks'])){
               $response['message']['global'] = "Nik value is required";
          }else{
               if(is_array($data['niks'])){
                    if(count($data['niks']) > 0){
                         $insert = array();

                         foreach($data['niks'] as $key){
                              $insert[] = array(
                                   'id_mitra' => $key['id_mitra'],
                                   'tanggal' => $key['tanggal'],
                                   'jam' => $key['jam'],
                                   'tempat' => $key['tempat'],
                                   'keterangan' => $key['keterangan']
                              );
                         }

                         $add = $this->model_register->saveinterview($insert);
                         if($add['success']){
                              $push = $this->pushnotif($data['niks']);
                              if($push['success']){
                                   $response['status'] = true;
                                   $response['message']['global'] = "Notification has been send";
                                   $response['info'] = $push['serveroutput'];
                              }else{
                                   $response['message']['global'] = "Insert failed!";
                                   $response['info'] = $push['serveroutput'];     
                              }
                         }else{
                              $response['message']['global'] = "Insert failed!";
                         }
                    }else{
                         $response['message']['global'] = "Cannot send empty array nik value";     
                    }
               }else{
                    $response['message']['global'] = "Nik value must in array";
               }
          }

          $this->response($response, 200);
     }

     private function pushnotif($values){
          $result   = array();
          $url      = getenv('PASTRU')."/notif";
          $payload  = array();

          foreach($values as $key){
               $payload[] = array(
                    'nik' => $key['nik'],
                    'nama' => $key['nama'],
                    'email' => $key['email'],
                    'tanggal' => $key['tanggal'],
                    'waktu' => $key['jam'],
                    'tempat' => $key['tempat'],
                    'keterangan' => $key['keterangan'],
                    'status_id' => 'S6'
               );
          }

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

          $headers = array();
          $headers[] = 'Accept: application/json';
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'X-POS-KEY: P05F1N';
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $server_output = json_decode(curl_exec($ch), true);
          $err = curl_error($ch);

          curl_close($ch);

          if(!$err){
               $result['success'] = true;
               $result['serveroutput'] = $server_output;
          }else{
               $result['success'] = false;
               $result['serveroutput'] = $err;
          }

          return $result;
     }

     public function ceknik_post(){
          $response['status']  = false;
          $response['message'] = "Internal server error";

          $data = $this->post();
          if(!isset($data['nik'])){
               $response['message'] = "Nik is required";
          }else{
               $isUsed = $this->model_register->ceknik($data['nik']);
               if($isUsed){
                    $response['message'] = "Nik already used";
               }else{
                    $response['status'] = true;
                    $response['message'] = "Nik available";
               }
          }


          $this->response($response, 200);
     }

     public function updatestatus_post(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";

          $data = $this->post();

          if(!isset($data['nik'])){
               $response['message']['nik'] = "Nik is required";
          }else{
               $config = array(
                    array('field' => 'id_mitra', 'label' => 'id mitra', 'rules' => 'required'),
                    array('field' => 'status_id', 'label' => 'status', 'rules' => 'required'),
                    array('field' => 'nik', 'label' => 'nik', 'rules' => 'required'),
                    array('field' => 'description', 'label' => 'description', 'rules' => 'required')
               );
               

               $this->form_validation->set_data($data);
               $this->form_validation->set_rules($config);
               if($this->form_validation->run() === TRUE){
                    $update = $this->model_register->updatestatus($data);
                    if($update['success']){
                         $data['platform'] = 'dev';
                         $push = $this->pushstatus($data);
                         if($push['success']){
                              $response['status'] = true;
                              $response['message']['global'] = "Update status berhasil";
                              $response['info'] = $push['serveroutput'];
                         }else{
                              $response['message']['global'] = "Insert failed!";
                              $response['info'] = $push['serveroutput'];     
                         }
                    }else{
                         $response['message']['global'] = "Update failed, no mitra found";
                    }
               }else{
                    $response['message'] = $this->form_validation->error_array();
               }
          }

          $this->response($response, 200);
     }


     private function pushstatus($values){
          $result   = array();
          $url      = getenv('PASTRU')."/updatestatus";

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($values));

          $headers = array();
          $headers[] = 'Accept: application/json';
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'X-POS-KEY: P05F1N';
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $server_output = json_decode(curl_exec($ch), true);
          $err = curl_error($ch);

          curl_close($ch);

          if(!$err){
               $result['success'] = true;
               $result['serveroutput'] = $server_output;
          }else{
               $result['success'] = false;
               $result['serveroutput'] = $err;
          }

          return $result;
     }
}
?>