    <div class="col-md-12">
      			<?=form_open("cicilan_pinjaman/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_cicilan_pinjaman"],$model)?>
			<?=form_hidden("cicilan_id")?>
			<?=form_hidden("pinjaman_id")?>
			<?=create_input("pinjaman_no")?>
			<?=create_input("employee_code=kode pegawai",["disabled"=>true])?>
			<?=create_input("employee_name=nama pegawai",["disabled"=>true])?>
			<?=create_input("nominal_pinjaman",["disabled"=>true])?>
			<?=create_input("tenor_pinjaman",["disabled"=>true])?>
			<?=create_input("cicilan_perbulan",["disabled"=>true])?>
			<?=create_input("jml_kali_cicilan",["disabled"=>true])?>
			<?=create_input("cicilan_no")?>
			<?=create_inputDate("cicilan_date",["format"=>"yyyy-mm-dd","autoclose"=>true])?>
			<?=create_input("qty_cicilan")?>
			<?=create_input("jml_cicilan",["readonly"=>true])?>
			<?=create_input("cicilan_note")?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_cicilan_pinjaman').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_cicilan_pinjaman").hide();
		$("#form_cicilan_pinjaman").html('');
	});
	$("body").on("focus", "#pinjaman_no", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('cicilan_pinjaman/get_pinjaman');?>",
            select: function (event, ui) {
                $('#employee_code').val(ui.item.emp_no);
                $('#employee_name').val(ui.item.emp_name);
                $('#pinjaman_id').val(ui.item.pinjaman_id);
                $('#nominal_pinjaman').val(ui.item.nominal_pinjaman);
                $('#tenor_pinjaman').val(ui.item.tenor_pinjaman);
                $('#cicilan_perbulan').val(ui.item.cicilan_perbulan);
                $('#jml_kali_cicilan').val(ui.item.jml_kali_cicilan);
            }
        });
	});
	$("#qty_cicilan").change(()=>{
		hitung_angsuran();
	});

	function hitung_angsuran() {
		let jml_utang 	= parseInt($("#cicilan_perbulan").val());
		let qty 		= parseInt($("#qty_cicilan").val());

		let angsuran = Math.ceil(jml_utang*qty);
		$("#jml_cicilan").val(angsuran);
	}
  <?=$this->config->item('footerJS')?>
</script>