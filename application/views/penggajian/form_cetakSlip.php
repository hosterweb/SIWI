<div class="col-md-12">
      	<?=form_open("penggajian/cetak_multiple",["method"=>"post","target"=>"_blank","id"=>"fm_cetak"])?>
			<?=create_inputDate("gaji_month",[
				"format"		=>"mm-yyyy",
				"viewMode"		=> "year",
				"minViewMode"	=> "year",
				"autoclose"		=>true],"required")?>
			<?=create_select2([
							"attr" =>["name"=>"unit_id=department","id"=>"unit_id","class"=>"form-control"],
							"model"=>["m_ms_department" => ["get_ms_department",["department_active"=>'t']],
											"column"  => ["department_id","nama_deparment"]
										],
						])?>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_cetak').submit()">Cetak</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#modal_cetak").modal('hide');
	});
  <?=$this->config->item('footerJS')?>
</script>