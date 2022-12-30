<style type="text/css">
	.table {
    border-collapse: collapse;
}

th {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
    font-weight: 100pt;
}
.table td {
  padding: 5px;
}
tfoot tr:first-child td {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

.body-tabel td {
  border: 0px !important;
  border-style: none !important;
  border-color: #fff !important;
}
.header th,td {
  padding:0px; margin:0px;
}

hr {
  line-height: 0px;
}

h4,h3 {
  font-weight: 150pt;
}

body {
  font-family: arial;
}
.hilang {
    display: none !important;
}
.text-center {
	text-align: center;
}
</style>
<h3 align="center">Laporan Data ABSENSI Pegawai</h3>
<table>
  <tr>
    <td>Department</td>
    <td>:</td>
    <td><?=$department?></td>
  </tr>
  <tr>
    <td>Bulan</td>
    <td>:</td>
    <td><?=$bulan?></td>
  </tr>
  <tr>
    <td>Keterangan</td>
    <td>:</td>
    <td>
      <?php
          foreach (get_absen() as $key => $value) {
            echo $value['kode'].' = '.$value['text'].'; ';
          }
      ?>
    </td>
  </tr>
</table>
<table class="table" style="width: 100%;" border="1">
  <thead>
    <tr>
      <th rowspan="2">NO</th>
      <th rowspan="2">KODE PEGAWAI</th>
      <th rowspan="2">NAMA PEGAWAI</th>
      <th colspan="31">BULAN <?=$bulan?></th>
    </tr>
    <tr>
      <?php
          for ($i=1; $i <= $last_date; $i++) { 
            echo "<td>$i</td>";
          }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
        foreach ($data as $key => $value) {
          echo "
              <tr>
                <td>($key+1)</td>
                <td>$value->emp_no</td>
                <td>$value->emp_name</td>
          ";
          $detail = explode("||",$value->detail);
          for ($i=1; $i <= $last_date; $i++) { 
            $tes="";
            foreach ($detail as $r) {
              $absen = explode("*-*",$r);
              if (date('d',strtotime($absen[0])) == $i) {
                $tes = array_search($absen[1], array_column(get_absen(), 'id'));
                $tes = get_absen()[$tes]['kode'];
                break;
              }
            }
            echo "<td style=\"text-align:center\">$tes</td>";
          }
          echo "</tr>";
        }
    ?>
  </tbody>
</table>