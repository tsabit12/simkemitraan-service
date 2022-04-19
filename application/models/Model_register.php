<?php

class Model_register extends CI_Model {
     public function getLastId($oranger){
          $id             = "";
          $this->db->select('RIGHT(id_mitra, 6) as id');
          $this->db->from('t_mitra');
          $this->db->where('LEFT(id_mitra, 3) =', $oranger);
          $this->db->order_by('id_mitra', 'DESC');
          $this->db->limit(1);
  
          $query = $this->db->get();
          if($query->num_rows() > 0){
              $data   = $query->row_array();
              $id     = $data['id'] + 1;
              $id     = str_pad($id, 6, "0", STR_PAD_LEFT);
              $id     = $oranger.$id;
          }else{
              $id     = 1;
              $id     = str_pad($id, 6, "0", STR_PAD_LEFT);
              $id     = $oranger.$id;
          }
  
          return $id;
     }

     public function cekoranger($value){
          $result['isvalid'] = false;
          $result['data'] = array();

          $this->db->select('keterangan, id_regmitra');
          $this->db->from('ref_jbt');
          $this->db->where('id_regmitra', $value);

          $q = $this->db->get();
          if($q->num_rows() > 0){
               $result['data']    = $q->row_array();
               $result['isvalid'] = true;
          }

          return $result;
     }

     public function cekkprk($value){
          $result['isvalid'] = false;

          $this->db->from('ref_kantor');
          $this->db->where('kprk', $value);

          $q = $this->db->get();
          if($q->num_rows() > 0){
               $result['isvalid'] = true;
          }

          return $result;
     }

     public function add($payload){
          $result['success']  = false;
          $id  = $this->getLastId($payload['oranger']);
          $npwp = '000000000000000';

          if(isset($payload['npwp'])){
               if(strlen($payload['npwp']) > 3){
                    $npwp = $payload['npwp'];
               }
          }

          $mitra = array(
               'id_mitra' => $id,
               'nama_mitra' => $payload['fullname'],
               'alamat' => $payload['nikaddress'],
               'no_ktp' => $payload['nik'],
               'tempat_lahir' => $payload['birthplace'],
               'tanggal_lahir' => $payload['birthday'],
               'jenis_kelamin' => $payload['gender'],
               'agama' => $payload['religion'],
               'no_hp' => $payload['phone'],
               'email' => $payload['email'],
               'jabatan' => $this->cekoranger($payload['oranger'])['data']['keterangan'],
               'kantor' => $payload['kprk'],
               'statusaktif' => '01',
               'id_status_karyawan' => 'S1',
               'source' => 'posfin'
          );

          $detail = array(
               'id_mitra' => $mitra['id_mitra'],
               'alamat_domisili' => $payload['domisiliaddress'],
               'status' => $payload['status'],
               'npwp' => $npwp
          );

          $nilai = array(
               array('id_mitra' => $mitra['id_mitra'], 'id' => 'C1', 'nilai' => '100', 'berkas' => $payload['berkas']['ijazah']),
               array('id_mitra' => $mitra['id_mitra'], 'id' => 'C2', 'nilai' => '100', 'berkas' => $payload['berkas']['ktp']),
               array('id_mitra' => $mitra['id_mitra'], 'id' => 'C3', 'nilai' => '100', 'berkas' => $payload['berkas']['sim']),
               array('id_mitra' => $mitra['id_mitra'], 'id' => 'C4', 'nilai' => '100', 'berkas' => $payload['berkas']['skck'])
          );

          $this->db->insert('t_mitra', $mitra);
          if($this->db->affected_rows() > 0){
               $this->db->insert('t_mitra_d', $detail);
               if($this->db->affected_rows() > 0){
                    $this->db->insert_batch('t_nilai', $nilai);
                    if($this->db->affected_rows() > 0){
                         $result['success'] = true;
                         $result['info'] = array(
                              'userid' => $mitra['id_mitra'],
                              'fullname' => $mitra['nama_mitra'],
                              'nik' => $mitra['no_ktp'],
                              'status' => $mitra['id_status_karyawan']
                         );
                    }else{
                         $this->db->where('t_mitra.id_mitra=t_mitra_d.id_mitra');
                         $this->db->where('t_mitra.id_mitra', $mitra['id_mitra']);
                         $this->db->delete(array('t_mitra', 't_mitra_d'));
                    }
               }else{
                    //rolback mitra
                    $this->db->where('id_mitra', $mitra['id_mitra']);
                    $this->db->delete('t_mitra');
               }
          }

          return $result;
     }

     public function saveinterview($insert){
          $result['success'] = false;

          $this->db->insert_batch('t_jadwal_interview_mitra', $insert);
          if($this->db->affected_rows() > 0){
               $update = array();
               foreach($insert as $key){
                    $update[] = array(
                         'id_mitra' => $key['id_mitra'],
                         'id_status_karyawan' => 'S6'
                    );
               }
               $this->db->update_batch('t_mitra',$update, 'id_mitra');
               
               $result['success'] = true;
          }

          return $result;
     }

     public function ceknik($nik){
          $this->db->from('t_mitra');
          $this->db->where('no_ktp', $nik);

          $q = $this->db->get();

          if($q->num_rows() > 0){
               return true;
          }else{
               return false;
          }
     }

     public function updatestatus($data){
          $result['success'] = false;

          $id = $data['id_mitra'];
          $this->db->where('id_mitra', $id);
          $this->db->update('t_mitra', array('id_status_karyawan' => $data['status_id']));

          if($this->db->affected_rows() > 0){
               $result['success'] = true;
          }

          return $result;
     }
}
?>