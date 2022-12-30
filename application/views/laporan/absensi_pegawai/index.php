<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Pelaporan Sistem')?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Layout</a></li>
        <li class="active">Fixed</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Laporan Absensi Pegawai</h3>
        </div>
        <div class="box-body">
        <?=form_open("laporan/rekap_absensi_pegawai/rekap_data",["target"=>"_blank","method"=>"post","id"=>"form_laporan_pegawai"])?>
            <?=create_inputDate("absensi_bulan",[
                "format"		=>"yyyy-mm",
                "viewMode"		=> "year",
                "minViewMode"	=> "year",
                "autoclose"		=>true],"required")
            ?>
            <?=create_select2([
							"attr" =>["name"=>"unit_id=department","id"=>"unit_id","class"=>"form-control"],
							"model"=>["m_ms_department" => ["get_ms_department",["department_active"=>'t']],
											"column"  => ["department_id","nama_deparment"]
										],
                  ],"required")?>
            <?=create_select([
							"attr"=>["name"=>"emp_type=Tipe Pegawai","id"=>"emp_type","class"=>"form-control"],
							"option"=> [["id"=>'1',"text"=>"Non Shift"],["id"=>'2',"text"=>"Shift"]],
						],"required")?>  
        </div>
        <div class="box-footer">
          <button class="btn btn-info" id="btn-data"><i class="fa fa-eye"></i> Tampilkan</button>
        <?=form_close()?>
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script type="text/javascript">	
	<?= $this->config->item('footerJS')?>
</script>