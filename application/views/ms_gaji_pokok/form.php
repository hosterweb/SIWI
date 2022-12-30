    <div class="col-md-12">
      			<?=form_open("ms_gaji_pokok/save",["method"=>"post","id"=>"fm_ms_gaji_pokok"],$model)?>
		<?=form_hidden("id_gp")?>
			<?=create_input("kode_gp")?>
			<?=create_select([
							"attr" =>["name"=>"jabatan_id=jabatan","id"=>"jabatan_id","class"=>"form-control"],
							"model"=>["m_ms_jabatan" => ["get_jabatan",["jabatan_aktif"=>'t']],
											"column"  => ["id_jabatan","nama_jabatan"]
										],
						])?>
			<?=create_select([
							"attr"=>["name"=>"karyawan_type","id"=>"karyawan_type","class"=>"form-control"],
							"option"=> ["Tetap","Outsourcing"],
						])?>
			<?=create_inputmask("nominal",["IDR",["rightAlign"=>false]])?>
			<?=create_select([
							"attr"=>["name"=>"gp_type=jenis gaji","id"=>"gp_type","class"=>"form-control"],
							"option"=> ["harian","bulanan"],
						])?>
			<?=create_select([
							"attr"=>["name"=>"gp_status=status","id"=>"gp_status","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" onclick="$('#fm_ms_gaji_pokok').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_gaji_pokok").hide();
		$("#form_ms_gaji_pokok").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>