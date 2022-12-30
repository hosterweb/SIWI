    <div class="col-md-12">
      			<?=form_open("pinjaman_pegawai/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_pinjaman_pegawai"],$model)?>
			<?=form_hidden("pinjaman_id")?>
			<?=form_hidden("emp_id")?>
			<?=create_input("pinjaman_no")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_inputDate("pinjaman_date",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_inputmask("nominal_pinjaman",["IDR",["rightAlign"=>false]])?>
			<?=create_input("tenor_pinjaman")?>
			<?=create_input("bunga_pinjaman")?>
			<?=create_inputmask("cicilan_perbulan",["IDR",["rightAlign"=>false]],["readonly"=>true])?>
			<?=create_input("pinjaman_note")?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_pinjaman_pegawai').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_pinjaman_pegawai").hide();
		$("#form_pinjaman_pegawai").html('');
	});

	$("#fm_pinjaman_pegawai").submit(function(){
		$("#cicilan_perbulan").removeAttr("readonly");
	});
	
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('pinjaman_pegawai/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
            }
        });
	});

	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('pinjaman_pegawai/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
            }
        });
	});

	$("#bunga_pinjaman, #nominal_pinjaman, #tenor_pinjaman").change(function(){
		hitung_angsuran();
	});

	function hitung_angsuran() {
		let jml_utang 	= parseInt((isNaN($("#nominal_pinjaman").val())?0:$("#nominal_pinjaman").val()));
		let tenor 		= parseInt((isNaN($("#tenor_pinjaman").val()))?0:$("#tenor_pinjaman").val());
		let bunga 		= parseFloat(isNaN($("#bunga_pinjaman").val())?0:$("#bunga_pinjaman").val());

		let angsuran = Math.ceil((jml_utang/tenor) + (jml_utang*bunga/100));
		$("#cicilan_perbulan").val(angsuran);
	}
  <?=$this->config->item('footerJS')?>
</script>