    <div class="col-md-12">
      	<?=form_open("formula_pajak_karyawan/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_formula_pajak_karyawan"],$model)?>
			<?=form_hidden("id_formula")?>
			<?=create_input("nama_pajak")?>
			<?=create_select([
				"attr"=>["name"=>"status_perkawinan","id"=>"status_perkawinan","class"=>"form-control"],
				"option"=> get_status(),
			])?>
			<?=create_input("jumlah_tanggungan")?>
			<?=create_select([
							"attr"=>["name"=>"tipe_penggajian","id"=>"tipe_penggajian","class"=>"form-control"],
							"option"=> ["harian","bulanan"],
						])?>
			<?=create_inputmask("limit_max_gaji",["IDR",["rightAlign"=>false]])?>
			<?=create_inputmask("nominal_potongan",["IDR",["rightAlign"=>false]])?>
			<?=create_input("persentase_potongan")?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_formula_pajak_karyawan').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_formula_pajak_karyawan").hide();
		$("#form_formula_pajak_karyawan").html('');
	});
<?=$this->config->item('footerJS')?>
</script>