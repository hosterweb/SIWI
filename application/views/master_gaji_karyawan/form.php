    <div class="col-md-12">
    	<div class="row">
      			<?=form_open("master_gaji_karyawan/save",["method"=>"post","id"=>"fm_master_gaji_karyawan"],$model)?>
			<?=form_hidden("id")?>
			<?=form_hidden("emp_id")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_input("jabatan")?>
			<?=create_input("gaji_pokok")?>
			<?=create_select([
							"attr"=>["name"=>"status","id"=>"status","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
			<?=form_close()?>
		</div>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_master_gaji_karyawan').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_master_gaji_karyawan").hide();
		$("#form_master_gaji_karyawan").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('master_gaji_karyawan/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
                $('#jabatan').val(ui.item.nama_jabatan);
                $('#gaji_pokok').val(ui.item.nominal);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('master_gaji_karyawan/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
                $('#jabatan').val(ui.item.nama_jabatan);
                $('#gaji_pokok').val(ui.item.nominal);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>