    <div class="col-md-12">
      	<?=form_open("ms_potongan/save",["method"=>"post","id"=>"fm_ms_potongan"],$model)?>
		<?=form_hidden("potongan_id")?>
			<?=create_input("nama_potongan")?>
			<?=create_input("percentase")?>
			<?=create_inputmask("nominal",["IDR",["rightAlign"=>false]])?>
			<?=create_select([
							"attr"=>["name"=>"status_potongan","id"=>"status_potongan","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" onclick="$('#fm_ms_potongan').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_potongan").hide();
		$("#form_ms_potongan").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>