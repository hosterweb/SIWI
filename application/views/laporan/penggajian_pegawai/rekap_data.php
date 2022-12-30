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
<h3 align="center">Laporan Data Penggajian Pegawai</h3>
<?=create_report_custom([
    "ext" 		=> ['class'=>'table','border'=>'1','width'=>'100%'],
    "name" 		=> "tb_gaji",
    "column" 	=> [
                    "emp_no",
                    "emp_name",
                    "nama_jabatan",
                    "department",
                    "gaji_brutto"=>["sumable"=>true,"masking"=>true],
                    "gaji_potongan"=>["sumable"=>true,"masking"=>true],
                    "gaji_netto"=>["sumable"=>true,"masking"=>true]
                    ],
    "data" 		=> $data,
])?>