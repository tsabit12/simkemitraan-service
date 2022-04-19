<?php

class Model_referensi extends CI_Model {
     public function oranger(){
          $result['success'] = false;
          
          $this->db->select('id_regmitra as value, keterangan as description, id_jbt');
          $this->db->from('ref_jbt');
          $this->db->where_in('id_regmitra', array('590','560','550'));
          $this->db->where('id_jbt <>', 'J05');

          $q = $this->db->get();
          if($q->num_rows() > 0){
               $result['success'] = true;
               $result['result'] = $q->result_array();
          }


          return $result;
     }   

     public function status($data){
          $result['success'] = false;

          $this->db->select('id_status_karyawan as statusid, keterangan as description');
          $this->db->from('ref_status_karyawan');

          if(isset($data['statusid'])){
               $this->db->where('id_status_karyawan', $data['statusid']);
          }

          $q = $this->db->get();

          if($q->num_rows() > 0){
               $result['success'] = true;
               $result['result'] = $q->result_array();
          }

          return $result;
     }

     public function office($data){
          $result['success'] = false;

          $this->db->select('nopend, NamaKtr as officename, idwilayah as regionid, alamat as address');
          $this->db->from('ref_kantor');

          if(isset($data['kprk'])){
               $this->db->where('nopend', $data['kprk']);
          }

          $this->db->where('nopend = kprk', null, false);
          $this->db->order_by('nopend', 'ASC');

          $q = $this->db->get();
          
          if($q->num_rows() > 0){
               $result['success'] = true;
               $result['result'] = $q->result_array();
          }

          return $result;
     }

     public function getkuota($kprk){
          $result['success'] = false;

          // $this->db->select('jml_loket, jml_antaran');
          // $this->db->from('a_r_formasi');
          // $this->db->where('kprk', $kprk);

          $this->db->select('sum(a.jml_antaran - t3.jumlah) as jml_antaran, sum(a.jml_loket - t2.jumlah) as jml_loket');
		$this->db->select('ISNULL(sum(t2.jumlah), 0) as jml_ini_loket');
		$this->db->select('ISNULL(sum(t3.jumlah), 0) as jml_ini_ant');
		$this->db->from('a_r_formasi a');
		$this->db->join("
			(SELECT COUNT(a.nopend) as jumlah, a.nopend
			FROM t_pks a, t_mitra b 
			WHERE a.id_mitra = b.id_mitra 
			AND LEFT(a.id_mitra, 3) = '550'
			AND status_pks = '1P' GROUP BY a.nopend) as t2
		", 'a.nopend = t2.nopend', 'LEFT', NULL);
		$this->db->join("
			(SELECT COUNT(a.nopend) as jumlah, a.nopend
			FROM t_pks a, t_mitra b 
			WHERE a.id_mitra = b.id_mitra 
			AND LEFT(a.id_mitra, 3) = '560'
			AND status_pks = '1P' GROUP BY a.nopend) as t3
		", 'a.nopend = t3.nopend', 'LEFT', NULL);
		$this->db->where('a.kprk', $kprk);

          $q = $this->db->get();

          if($q->num_rows() > 0){
               $data = $q->row_array();
               $result['success'] = true;
               $result['kuota_loket'] = $data['jml_loket'];
               $result['kuota_antaran'] = $data['jml_antaran'];
          }


          return $result;

     }
}

?>