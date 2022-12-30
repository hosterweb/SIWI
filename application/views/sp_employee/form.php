    <div class="col-md-12">
      			<?=form_open("sp_employee/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_sp_employee"],$model)?>
			<?=form_hidden("sp_id")?>
			<?=form_hidden("employee_id")?>
			<?=create_input("sp_no")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_inputDate("sp_date",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_textarea("sp_note=isi SP")?>
			<?=create_select([
							"attr"=>["name"=>"type_offense=jenis pelanggaran","id"=>"type_offense","class"=>"form-control"],
							"option"=>[
										["id"=>"1","text"=>"Ringan"],["id"=>"2","text"=>"Sedang"],["id"=>"3","text"=>"Berat"]
									]
						])?>
			<?=create_select([
							"attr"=>["name"=>"sp_type=jenis SP","id"=>"sp_type","class"=>"form-control"],
							"option"=>["SP1","SP2","SP3"]
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_sp_employee').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_sp_employee").hide();
		$("#form_sp_employee").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('sp_employee/get_employee/name');?>",
            select: function (event, ui) {
                $('#employee_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('sp_employee/get_employee/code');?>",
            select: function (event, ui) {
                $('#employee_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>