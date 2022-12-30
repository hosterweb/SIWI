    <div class="col-md-12">
      		<?=form_open("dinas_luar/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_dinas_luar"],$model)?>
			<?=form_hidden("id_dinas")?>
			<?=create_input("no_dinas")?>
			<?=create_inputDate("tanggal_dinas",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_inputDaterange("periode=periode dinas",["locale"=>["format"=>"YYYY-MM-DD","separator"=>"/"]])?>
			<?=form_hidden("emp_id")?>
			<?=create_input("employee_code=kode pegawai",["required"=>true])?>
			<?=create_input("employee_name=nama pegawai",["required"=>true])?>
			<?=create_input("alat_transportasi")?>
			<?=create_input("tujuan_dinas")?>
			<?=create_inputMask("anggaran_biaya",["IDR",["rightAlign"=>false]])?>
			<?=create_input("pemberi_tugas")?>
			<?=create_textArea("keterangan_tugas")?>
			<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_dinas_luar').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_dinas_luar").hide();
		$("#form_dinas_luar").html('');
	});

	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('dinas_luar/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('dinas_luar/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});
	$("body").on("focus", "#pemberi_tugas", function() {
	    $(this).autocomplete({source: "<?php echo site_url('dinas_luar/get_employee/name');?>"});
	});

  <?=$this->config->item('footerJS')?>
</script>