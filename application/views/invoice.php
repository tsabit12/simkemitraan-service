<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Invoice </title>
<link href="report_suratjalan_view_data/style_DO.css" rel="stylesheet" type="text/css">
<table width="100%">
  <tr>
    <td align="center">
    <h1><u><B>INVOICE</B></u></h1>
    </td>
  </tr>
</table>
<table class="bottomBorder">
  <tr>
    <td align="center" >
     VENDOR NAME<BR>(PT. POS INDONESIA)
    </td>
  </tr>    
</table>
<br>
<?php
  function rupiah($angka){
      $hasil_rupiah = "" . number_format($angka,0,'.','.');
      return $hasil_rupiah;
  }
?>
<table  width="100%">
  <tr>
    <td align="left" width="50%">
    <table class="bottomBorder">
      <tr><td>
         Kepada :<BR>
         <?php echo $record['namakantor'];?><br>
         <?php echo $record['address1'];?><br>
         <?php echo $record['postalcode'];?>
      </td></tr>
   </table>
    </td>
    <td align="right" width="50%">
     Nomor Invoice : <?php echo $record['no_invoice'];?><BR>
     <br>
     Tanggal Invoice : <?php echo $record['tgl_invoice'];?><br>
    </td>
  </tr> 
</table>
<p>Berdasarkan dari dokumen Purchase Order dan Proses Pembuatan Order pengiriman melalui PT. Pos Indonesia. Dengan rincian sebagai berikut</p>
<table class="bottomBorder" width="100%" border="1">
  <tr>
    <td align="center" width="10%">No</td>
    <td align="center" width="30%">Nomor Purchase Order</td>
    <td align="center" width="30%">Deskripsi</td>
    <td align="center" width="10%">Kuantitas</td>
    <td align="center" width="20%">Harga Total</td>
  </tr>
</table>
<table width="100%" class="topBorder" >
  <?php
    $detail = $record['detail'];
    $no = 1;
    $sumFee = 0;
    $totPpn = 0;
    foreach ($detail as $key) {
      echo " <tr>
              <td align='center' width='10%'>".$no++."</td>
              <td align='center' width='30%'>".$key['id_po']."</td>
              <td align='center' width='30%'>".$key['line']."</td>
              <td align='center' width='10%'>".$key['jumlah']."</td>
              <td align='right' width='20%'>IDR ".rupiah($key['total'])."</td>
            </tr>";
      $sumFee = $sumFee + $key['total'];
      $totPpn = $totPpn + $key['ppn'];
    }
  ?>
</table>
<table align="right" width="20%" class="body">
  <tr>
    <td width="50%">Total</td>
    <td>IDR</td>
    <td><?php echo rupiah($sumFee);?></td>
  </tr>
  <tr>
    <td >PPN</td>
    <td>IDR</td>
    <td align="right">
        <?php 

          echo "".rupiah($totPpn)."";
          $totAll = $sumFee + $totPpn;
        ?>
    </td>
  </tr>
  <tr>
    <td >Grand Total</td>
    <td>IDR</td>
    <td align="right" ><?php echo rupiah($totAll);?></td>
  </tr>
</table>
<br><br><br><br><br><br><br><br>
<table width="100%">
  <tr>
    <td colspan="3" width="50%">
      <b>Pelunasan dilakukan via Transfer melalui Rekening dibawah ini : </b></td>
      <td align="center">Kepala Kantor</td>
    </tr>
    <tr>
      <td>Nama Bank       </td><td>:</td><td><b>PT Bank Rakyat Indonesia</b></td> 
      <td rowspan="3"></td>
    </tr>
    <tr>
    <td>Nomor Rekening    </td><td>:</td> <td><b>0230-01-000084-30-6 </b></td>
    </tr>
    <tr>
      <td>Atas Nama       </td><td>:</td><td><b>PT Pos Indonesia</b></td>
    </tr>     
    <tr>
      <td colspan="3">(Biaya transfer dibebankan penyetor)</td>
    </tr> 
    <tr>
      <td colspan="3"></td>
      <td align="center">(_______________________)</td>
    </tr>
</table>
<br>
*Pelunasan dilakukan paling lambat 14 (empat belas) hari setelah diterimanya surat tagihan ini. 


</body>
</html>

<style>
  table.bottomBorder { 
    border-collapse: collapse; 
  }
  table.bottomBorder td, 
  table.bottomBorder th { 
    border-top: 1px solid ;
    border-bottom: 1px solid ; 
    border-right: 1px solid ; 
    border-left:  1px solid ;
    
  }
  table.topBorder { 
    border-collapse: collapse; 
  }
  table.topBorder td, 
  table.topBorder th { 
    /*border-top: 1px solid ;*/
    border-bottom: 1px solid ; 
/*    border-right: 1px solid ; 
    border-left:  1px solid ; */
    padding: 10px; 
    
  }

  table.body { 
    border-collapse: collapse; 
  }
  table.body td, 
  table.body th { 
    padding: 10px; 
    
  }  
</style>