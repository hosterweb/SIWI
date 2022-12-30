    <div class="col-md-12">
      		<?=form_open("ms_tunjangan/save",["method"=>"post","id"=>"fm_ms_tunjangan"],$model)?>
			<?=form_hidden("tunjangan_id")?>
			<?=create_input("tunjangan")?>
			<?=create_inputmask("nominal",["IDR",["rightAlign"=>false]])?>
			<?=create_select([
							"attr"=>["name"=>"jenis_tunjangan","id"=>"jenis_tunjangan","class"=>"form-control"],
							"option"=>["Harian","Bulanan","Tahunan"]
						])?>
			<?=create_select([
							"attr"=>["name"=>"status_tunjangan","id"=>"status_tunjangan","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" onclick="$('#fm_ms_tunjangan').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_tunjangan").hide();
		$("#form_ms_tunjangan").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>