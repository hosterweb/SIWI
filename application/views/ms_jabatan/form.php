    <div class="col-md-12">
      		<?=form_open("ms_jabatan/save",["method"=>"post","id"=>"fm_ms_jabatan"],$model)?>
			<?=form_hidden("id_jabatan")?>
			<?=create_input("kode_jabatan")?>
			<?=create_input("nama_jabatan")?>
			<?=create_select([
								"attr" 		=> ["name"=>"jabatan_aktif","id"=>"jabatan_aktif","class"=>"form-control"],
								"option"	=>[
												["id"=>"t","text"=>"Aktif"],["id"=>"f","text"=>"Non Aktif"]
											  ]
							])?>
			<?=form_close()?>
      <div class="box-footer text-center">
      		<button class="btn btn-primary" onclick="$('#fm_ms_jabatan').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_jabatan").hide();
		$("#form_ms_jabatan").html('');
	});
	<?=$this->config->item('footerJS')?>
</script>