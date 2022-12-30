    <div class="col-md-12">
      	<?=form_open("ijin_dijam_kerja/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_ijin_dijam_kerja"],$model)?>
			<?=form_hidden("id")?>
			<?=form_hidden("emp_id")?>
			<?=create_input("no_ijin")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_inputDate("ijin_date",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
      		<?=create_inputmask("ijin_checkin",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
			<?=create_inputmask("ijin_checkout",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
			<?=create_textArea("ijin_note")?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_ijin_dijam_kerja').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ijin_dijam_kerja").hide();
		$("#form_ijin_dijam_kerja").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('ijin_dijam_kerja/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('ijin_dijam_kerja/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>