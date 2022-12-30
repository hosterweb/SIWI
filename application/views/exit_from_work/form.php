    <div class="col-md-12">
      			<?=form_open("exit_from_work/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_exit_from_work"],$model)?>
		<?=form_hidden("ex_id")?>
		<?=form_hidden("emp_id")?>
			<?=create_input("employee_code=Kode Pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>			
			<?=create_inputDate("date_ex=Tanggal Keluar",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			
			<?=create_select([
							"attr"=>["name"=>"ex_type=Tipe","id"=>"ex_type","class"=>"form-control"],
							"option"=>[
										["id"=>"1","text"=>"Keluar"],["id"=>"2","text"=>"PHK"]
									]
						])?>
			<?=create_textarea("ex_note=Catatan")?>
			<?=form_hidden("user_created")?>
			<?=form_hidden("created_at")?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_exit_from_work').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_exit_from_work").hide();
		$("#form_exit_from_work").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('sp_employee/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
		$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('exit_from_work/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
               
            }
        });
	});

  <?=$this->config->item('footerJS')?>
</script>