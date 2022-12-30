    <div class="col-md-12">
    	<?= form_open("pengajuan_cuti/save", ["method" => "post", "class" => "form-horizontal", "id" => "fm_pengajuan_cuti"], $model) ?>
    	<?= form_hidden("id") ?>
    	<?= form_hidden("emp_id") ?>
    	<?= create_input("no_pengajuan") ?>
    	<?= create_input("employee_code=kode pegawai") ?>
    	<?= create_input("employee_name=nama pegawai") ?>
		<?= create_select([
			"attr" => ["name" => "cuti_id=jenis cuti", "id" => "cuti_id", "class" => "form-control"],
			"model" => [
				"m_ms_cuti" => ["get_ms_cuti", ["cuti_status" => 't']],
				"column"  => ["cuti_id", "cuti_name"]
			],
		]) ?>
    	<?= create_input("cuti_terpakai", ["disabled" => true]) ?>
    	<?= create_input("sisa_cuti", ["disabled" => true]) ?>
    	<?= create_inputDaterange("tanggal=periode cuti", ["locale" => ["format" => "YYYY-MM-DD", "separator" => "/"]]) ?>
    	<?= create_input("jml_cuti", ["readonly" => true]) ?>
    	<?= create_textArea("cuti_keterangan") ?>
    	<?= create_input("berkas_cuti") ?>
    	<?= form_close() ?>
    	<div class="box-footer">
    		<button class="btn btn-primary" type="button" onclick="$('#fm_pengajuan_cuti').submit()">Save</button>
    		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
    	</div>
    </div>
    <script type="text/javascript">
    	$("#btn-cancel").click(() => {
    		$("#form_pengajuan_cuti").hide();
    		$("#form_pengajuan_cuti").html('');
    	});

    	$("#cuti_id").change(() => {
    		get_countCuti();
    	});

    	$("body").on("focus", "#employee_name", function() {
    		$(this).autocomplete({
    			source: "<?php echo site_url('pengajuan_cuti/get_employee/name'); ?>",
    			select: function(event, ui) {
    				$('#emp_id').val(ui.item.emp_id);
    				$('#employee_code').val(ui.item.emp_no);
    				get_countCuti();
    			}
    		});
    	});
    	$("body").on("focus", "#employee_code", function() {
    		$(this).autocomplete({
    			source: "<?php echo site_url('pengajuan_cuti/get_employee/code'); ?>",
    			select: function(event, ui) {
    				$('#emp_id').val(ui.item.emp_id);
    				$('#employee_name').val(ui.item.emp_name);
    				get_countCuti();
    			}
    		});
    	});
    	$("#tanggal").on('apply.daterangepicker', function(ev, picker) {
    		var start = moment(picker.startDate.format('YYYY-MM-DD'));
    		var end = moment(picker.endDate.format('YYYY-MM-DD'));
    		var diff = end.diff(start, 'days') + 1; // returns correct number
    		if (diff > $("#sisa_cuti").val()) {
    			alert("Jumlah ambil cuti melebihi sisa cuti");
    			$("#tanggal").val("");
    			return false;
    		}
    		$("#jml_cuti").val(diff);
    	});

    	function get_countCuti(edit = false) {
			let sukses=true;
    		$.get("pengajuan_cuti/get_sisa_cuti/" + $("#cuti_id").val() + '/' + $("#emp_id").val(), function(resp) {
    			if (resp.code == '200') {
					$("#sisa_cuti").val(resp.response.sisa_cuti);
    				$("#cuti_terpakai").val(resp.response.total_cuti);
				}else{
					alert(resp.message);
					sukses = false;
				}
    		}, 'json').then(()=>{
				if (!sukses) {
					$("#cuti_id").val('');
					$("#tanggal, #jml_cuti, #cuti_terpakai, #sisa_cuti").val("");
				}
			});

			if (!edit) {
				$("#tanggal, #jml_cuti").val("");
			}
			
    	}
    	<?= $this->config->item('footerJS') ?>
    </script>