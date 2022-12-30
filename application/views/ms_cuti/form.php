    <div class="col-md-12">
      			<?=form_open("ms_cuti/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_ms_cuti"],$model)?>
		<?=form_hidden("cuti_id")?>
			<?=create_input("cuti_name=Jenis Cuti")?>
			<?=create_input("cuty_qty=Jumlah Cuti")?>
			<?=create_textArea("cuti_note=Keterangan")?>
			<?=create_select([
							"attr"=>["name"=>"cuti_status=Status","id"=>"cuti_status","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_ms_cuti').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_cuti").hide();
		$("#form_ms_cuti").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>