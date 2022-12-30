    <div class="col-md-12">
      	<?=form_open("absensi_pegawai/go_import",["method"=>"post","id"=>"fm_import","enctype"=>"multipart/form-data"])?>
      	<?=create_inputDate("tanggal_awal",["format"=>"yyyy-mm-dd","autoclose"=>true],["required"=>true])?>
      	<?=create_inputDate("tanggal_akhir",["format"=>"yyyy-mm-dd","autoclose"=>true],["required"=>true])?>
		<div class="form-group">
		  <label>File Excel</label>
          <input type="file" name="file_excel" id="file_excel" required="true">
        </div>
		<?=form_close()?>
      <div class="box-footer text-center">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_import').submit()">Save</button>
      		<button class="btn btn-warning" type="button" data-dismiss="modal" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
  <?=$this->config->item('footerJS')?>
</script>