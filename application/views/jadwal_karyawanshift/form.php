    <div class="col-md-12">
      			<?=form_open("jadwal_karyawanshift/update",["method"=>"post","class"=>"form-horizontal","id"=>"fm_jadwal_karyawanshift"],$model)?>
			<?=form_hidden("jadwalkarywawan_id")?>
          	<?=create_switchbutton("karyawan_shift",[
          							"onText" 			=> "YA",
          							"offText" 			=> "TIDAK",
          							"onSwitchChange"	=> "function(){
										if($(this).is(':checked')){
											$(this).val(2);
											$('#tanggal').attr('disabled',false);
										  }else{
											$(this).val(1);
											$('#tanggal').attr('disabled',true);
										  }
          							}"
          						])?>
			<?=form_hidden("emp_id")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_inputDate("tanggal",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_select([
							"attr" =>["name"=>"jadwal_id=Jadwal","id"=>"jadwal_id","class"=>"form-control"],
							"model"=>["m_ms_jadwal" => ["get_ms_jadwal",["jadwal_active"=>'t']],
								"column"  => ["jadwal_id","keterangan_jadwal"]
							],
			])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_jadwal_karyawanshift').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_jadwal_karyawanshift").hide();
		$("#form_jadwal_karyawanshift").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('jadwal_karyawanshift/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('jadwal_karyawanshift/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>