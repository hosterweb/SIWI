    <div class="col-md-12">
      			<?=form_open("absensi_pegawai/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_absensi_pegawai"],$model)?>
		<?=form_hidden("absen_id")?>
			<?=create_input("emp_absen_code",["readonly"=>true])?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_inputDate("absen_date",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_input("check_in")?>
			<?=create_input("check_out")?>
			<?=create_input("late_duration")?>
			<?=create_select([
							"attr"=>["name"=>"absen_type","id"=>"absen_type","class"=>"form-control"],
							"option"=> get_absen(),
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_absensi_pegawai').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_absensi_pegawai").hide();
		$("#form_absensi_pegawai").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('absensi_pegawai/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_absen_code').val(ui.item.absen_code);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('absensi_pegawai/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_absen_code').val(ui.item.absen_code);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
</script>