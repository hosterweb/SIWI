    <div class="col-md-12">
      			<?=form_open("ms_department/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_ms_department"],$model)?>
		<?=form_hidden("department_id")?>
			<?=create_input("department_code=Kode")?>
			<?=create_input("department_name=Nama Departemen")?>
			<?=create_input("department_div=Divisi")?>
			<?=create_input("department_sect=Sektor")?>
			<?=create_input("department_subsect=Sub Sektor")?>
			<?=create_input("kelompok")?>
			<?=create_select([
							"attr"=>["name"=>"department_active=Status","id"=>"department_active","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_ms_department').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#form_ms_department").hide();
		$("#form_ms_department").html('');
	});

  <?=$this->config->item('footerJS')?>
</script>