<?php
class Model_transaksi extends CI_Model {
    public function insertData($data){
        $result['success'] = false;
        $insert = array();

        foreach($data as $key){
            $insert[] = array(
                'id_mitra' => $key['mitra_id'],
                'tgl_trans' => $key['transaction_date'],
                'id_produk' => $this->mappingProduk($key['service_id']),
                'id_backsheet' => $key['backsheet_id'],
                'nopend' => $key['office_code'],
                'jml' => $key['produksi'],
                'jml_bsu' => $key['bsu'],
                'status_apprv' => '',
                'id_sistem' => '03',
                'jenis_produk' => '01',
                'id_produk_real' => $this->mappingProduk($key['service_id']),
                'id_trans' => '00',
                'isnippos' => '1'
            );
        }

        $this->db->db_debug = FALSE; 
        $insert = $this->db->insert_batch('t_transaksi_new', $insert);
        
        if($insert){
            if($this->db->affected_rows() > 0){
                $result['success'] = true;
            }
        }

        return $result;
    }

    public function insertDataAntaran($data){
        $result['success'] = true;
        $insert = array();

        foreach($data as $key){
            $insert[] = $key;
        }

        $this->db->db_debug = FALSE; 
        $add = $this->db->insert_batch('KINERJA_ANTARAN_KEMITRAAN_PID_API', $insert);

        if($add){
            if($this->db->affected_rows() > 0){
                $result['success'] = true;
            }
        }
        
        return $result;
    }

    private function mappingProduk($value){
        $produkid = trim(strtolower($value), " ");
        switch ($produkid) {
            case 'pkh':
                return '240';
                break;
            case 'q9':
                return '2Q9';
                break;
            case 'pe':
                return '447';
                break;
            case 'pje':
                return 'PJE';
                break;
            default:
                return $produkid;
                break;
        }
    }
}

?>