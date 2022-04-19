<?php
use Restserver\Libraries\REST_Controller;
require(APPPATH.'/libraries/REST_Controller.php');

class ReferensiApri extends REST_Controller{
    public function __construct(){
		parent::__construct();
        $this->load->library('form_validation');
    }
    public function index_get(){
        
    }

    public function refKantor_post()
    {
        $param = $this->post();
        $jenis = $param['jenis'];
        $sql = $this->db->query("EXEC db_kemitraan.dbo.spGetKantorJenis @jenis='$jenis'");
        if($sql->num_rows() > 0){
            $response['rscode'] = 200;
            $response['data'] = $sql->result_array();
        }else{
            $response['rscode'] = 200;
            $response['data'] = array();
        }
        $this->response($response, 200);
    }
}
?>