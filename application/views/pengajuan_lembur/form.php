    <div class="col-md-12">
      	<?=form_open("pengajuan_lembur/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_pengajuan_lembur"],$model)?>
			<?=form_hidden("id")?>
			<?=form_hidden("emp_id")?>
			<?=create_input("lembur_no=No Lembur")?>
			<?=create_input("employee_code=Kode Pegawai")?>
			<?=create_input("employee_name=Nama Pegawai")?>
			<?=create_inputDate("lembur_date=Tanggal",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_inputmask("lembur_start=Jam Mulai",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
			<?=create_inputmask("lembur_end=Jam Selesai",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
			<?=create_textArea("lembur_task=Tugas Lembur")?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_pengajuan_lembur').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_pengajuan_lembur").hide();
		$("#form_pengajuan_lembur").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('pengajuan_cuti/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('pengajuan_cuti/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>