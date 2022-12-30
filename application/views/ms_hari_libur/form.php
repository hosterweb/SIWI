    <div class="col-md-12">
      		<?=form_open("ms_hari_libur/save",["method"=>"post","id"=>"fm_ms_hari_libur"],$model)?>
			<?=form_hidden("libur_id")?>
			<?=create_input("nama_libur")?>
			<?=create_inputDate("tanggal",["format"=>"yyyy-mm-dd","autoclose"=>"true"])?>
			<?=create_select([
							"attr"=>["name"=>"jenis_libur","id"=>"jenis_libur","class"=>"form-control"],
							"option"=>[
								["id"=>"1","text"=>"Hari Libur"],["id"=>"2","text"=>"Cuti Bersama"]
									]
						])?>
			<?=create_input("keterangan")?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" onclick="$('#fm_ms_hari_libur').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_hari_libur").hide();
		$("#form_ms_hari_libur").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>