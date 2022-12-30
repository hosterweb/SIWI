    <div class="col-md-12">
		<?=form_open("ms_jadwal/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_ms_jadwal"],$model)?>
		<?=form_hidden("jadwal_id")?>
		<?=create_input("keterangan_jadwal")?>
		<?=create_inputmask("jam_masuk",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
		<?=create_inputmask("jam_pulang",['datetime', ['inputFormat'=>'HH:MM:ss',"placeholder"=>"00:00:00"]])?>
		<?=create_select([
							"attr"=>["name"=>"shift","id"=>"shift","class"=>"form-control"],
							"option"=> get_dataShift()
						])?>
		<?=create_select([
							"attr"=>["name"=>"jadwal_active=Status","id"=>"jadwal_active","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_ms_jadwal').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_jadwal").hide();
		$("#form_ms_jadwal").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>