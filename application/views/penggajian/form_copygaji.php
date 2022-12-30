<div class="col-md-12">
	<?= form_open("penggajian/copy_gaji", ["method" => "post", "id" => "fm_penggajian"]) ?>
	<?= create_inputDaterange("tanggal=periode gaji", ["locale" => ["format" => "YYYY-MM-DD", "separator" => "/"]]) ?>
	<?= create_inputDate("gaji_asal=Dari Bulan", [
		"format"		=> "mm-yyyy",
		"viewMode"		=> "year",
		"minViewMode"	=> "year",
		"autoclose"		=> true
	], "required") ?>
	<?= create_inputDate("gaji_month=Untuk Bulan", [
		"format"		=> "mm-yyyy",
		"viewMode"		=> "year",
		"minViewMode"	=> "year",
		"autoclose"		=> true
	], "required") ?>
	<?= create_input("gaji_note") ?>
	<?= form_close() ?>
	<div class="box-footer">
		<button class="btn btn-primary" type="button" onclick="$('#fm_penggajian').submit()">Save</button>
		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
	</div>
</div>
<script type="text/javascript">
	$("#tanggal").on('apply.daterangepicker', function(ev, picker) {
		var start = moment(picker.startDate.format('YYYY-MM-DD'));
		var end = moment(picker.endDate.format('YYYY-MM-DD'));
		let diff = parseInt(end.diff(start, 'days')) + 1; // returns correct number
		jml_hari = diff;
		jml_income();
	});

	$("#btn-cancel").click(() => {
		$("#form_penggajian").hide();
		$("#form_penggajian").html('');
	});
	<?= $this->config->item('footerJS') ?>
</script>