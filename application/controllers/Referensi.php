<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Referensi extends REST_Controller{
     public function __construct(){
		parent::__construct();
          $this->load->model('model_referensi');
     }

     public function oranger_get(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";

          $data = $this->model_referensi->oranger();
          if($data['success']){
               $response['status'] = true;
               $response['message'] = new StdClass();
               $response['transref'] = $data['result'];
          }

          $this->response($response, 200);
     }

     public function status_get(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";

          $field = $this->get();

          $data = $this->model_referensi->status($field);
          if($data['success']){
               $response['status'] = true;
               $response['message'] = new StdClass();
               $response['transref'] = $data['result'];
          }

          $this->response($response, 200);
     }

     public function religion_get(){
          $data = array(
               array('description' => 'Islam'),
               array('description' => 'Kristen Protestan'),
               array('description' => 'Katolik'),
               array('description' => 'Hindu'),
               array('description' => 'Buddha'),
               array('description' => 'Kong Hu Cu'),
               array('description' => 'Kepercayaan Lain')
          );

          $this->response(array(
               'status' => true,
               'message' => new StdClass(),
               'transref' => $data
          ), 200);
     }

     public function office_get(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";

          $field = $this->get();

          $data = $this->model_referensi->office($field);

          if($data['success']){
               $response['status'] = true;
               $response['message'] = new StdClass();
               $response['transref'] = $data['result'];
          }

          $this->response($response, 200);
     }

     public function kuota_get(){
          $response['status'] = false;
          $response['message']['global'] = "Internal server error";
          $data = $this->get();

          if(!isset($data['kprk'])){
               $response['message']['kprk'] = "Kprk field is required";
               unset($response['message']['global']);
          }else{
               $config = array(array('field' => 'kprk', 'label' => 'kprk', 'rules' => 'required|integer|max_length[5]'));
               $this->form_validation->set_data($data);
               $this->form_validation->set_rules($config);

               if($this->form_validation->run() === FALSE){
                    $response['message'] = $this->form_validation->error_array();
               }else{
                    $data = $this->model_referensi->getkuota($data['kprk']);
                    if($data['success']){
                         $response['status'] = true;
                         $response['kuota_loket'] = $data['kuota_loket'];
                         $response['kuota_antaran'] = $data['kuota_antaran'];
                         unset($response['message']);
                    }else{
                         $response['message']['global'] = "Data not found";
                    }
               }
          }


          $this->response($response, 200);
     }

}