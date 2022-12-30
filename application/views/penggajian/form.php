    <div class="col-md-12">
      	<?=form_open("penggajian/save",["method"=>"post","class"=>"form-horizontal","id"=>"fm_penggajian"],$model)?>
		<?=form_hidden("gaji_id")?>
			<?=create_input("gaji_no")?>
			<?=create_inputDaterange("tanggal=periode gaji",["locale"=>["format"=>"YYYY-MM-DD","separator"=>"/"]])?>
			<?=create_inputDate("gaji_month",[
				"format"		=>"mm-yyyy",
				"viewMode"		=> "year",
				"minViewMode"	=> "year",
				"autoclose"		=>true],"required")?>
			<?=form_hidden("emp_id")?>
			<?=create_input("employee_code=kode pegawai")?>
			<?=create_input("employee_name=nama pegawai")?>
			<?=create_input("gaji_note")?>
			<?=form_hidden("gaji_brutto")?>
			<?=form_hidden("gaji_potongan")?>
			<?=form_hidden("gaji_netto")?>
		<div class="row">
			<div class="col-md-6">
				<div class="box box-primary box-solid">
					<div class="box-header">Penerimaan</div>
					<div class="div_terima box-body">
						
					</div>
					<div class="box-footer">
						<span class="text-left">Total Penerimaan : </span>
						<span class="text-right"><b id="txt_total_terima">0</b></span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-danger box-solid">
					<div class="box-header">Potongan</div>
					<div class="div_potongan box-body">
						
					</div>
					<div class="box-footer">
						<span class="text-left">Total Potongan : </span>
						<span class="text-right"><b id="txt_total_potong">0</b></span>
					</div>
				</div>
			</div>
		</div>
		<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_penggajian').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
let jml_hari=0;
	$(document).ready(()=>{
	    $(".div_terima").inputMultiRow({
	            column: ()=>{
	                return [
		            		{
		            			"id":"keterangan",
			            		"label":"keterangan",
			            		"type":"text"
		            		}
		            		,
		            		{
		            			"id":"qty",
			            		"label":"QTY",
			            		"type":"text"
		            		},
		            		{
		            			"id":"nominal",
			            		"label":"nominal",
			            		"type":"text"
		            		}
	            		];
	                },
                // "data": obj
	          });
	    $(".div_potongan").inputMultiRow({
	            column: ()=>{
	                return [
		            		{
		            			"id":"keterangan",
			            		"label":"keterangan",
			            		"type":"text"
		            		},
		            		{
		            			"id":"nominal",
			            		"label":"nominal",
			            		"type":"text"
		            		}
	            		];
	                }
	          });
	});

	$("#tanggal").on('apply.daterangepicker', function(ev, picker) {
            var start = moment(picker.startDate.format('YYYY-MM-DD'));
            var end   = moment(picker.endDate.format('YYYY-MM-DD'));
            let diff = parseInt(end.diff(start, 'days'))+1; // returns correct number
            jml_hari = diff;
            jml_income();
     });

	$("#btn-cancel").click( () => {
		$("#form_penggajian").hide();
		$("#form_penggajian").html('');
	});
	$("body").on("focus", "#employee_name", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('penggajian/get_employee/name');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_code').val(ui.item.emp_no);
                get_komponen_gaji(ui.item.emp_id);
            }
        });
	});
	$("body").on("focus", "#employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('penggajian/get_employee/code');?>",
            select: function (event, ui) {
                $('#emp_id').val(ui.item.emp_id);
                $('#employee_name').val(ui.item.emp_name);
                get_komponen_gaji(ui.item.emp_id);
            }
        });
	});
	$(".div_terima").on("focus", ".keterangan", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('penggajian/get_gaji_terima');?>",
            select: function (event, ui) {
                $(this).closest('tr').find('.nominal').val(ui.item.nominal);
                jml_income();
            }
        });
	});
	$(".div_terima").on("click", ".removeItem_div_terima", function() {
	    setTimeout(function(){
	    	jml_income();
	    },'100')
	});
	$(".div_potongan").on("click", ".removeItem_div_potongan", function() {
	    setTimeout(function(){
	    	jml_potongan();
	    },'100')
	});
	$(".div_potongan").on("focus", ".keterangan", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('penggajian/get_gaji_potongan');?>",
            select: function (event, ui) {
                $(this).closest('tr').find('.nominal').val(ui.item.nominal);
                jml_potongan();
            }
        });
	});

	function get_komponen_gaji(id) {
		$.get("penggajian/get_komponen_gaji/"+id,function(resp){
			$(".div_terima").inputMultiRow({
	            column: ()=>{
	                return [
		            		{
		            			"id":"keterangan",
			            		"label":"keterangan",
			            		"type":"text"
		            		},
		            		{
		            			"id":"qty",
			            		"label":"QTY",
			            		"type":"text"
		            		},
		            		{
		            			"id":"nominal",
			            		"label":"nominal",
			            		"type":"text"
		            		}
	            		];
	                },
                "data": resp.terima
	          });
			$(".div_terima").find('.nominal').trigger('change');
		    $(".div_potongan").inputMultiRow({
		            column: ()=>{
		                return [
			            		{
			            			"id":"keterangan",
				            		"label":"keterangan",
				            		"type":"text"
			            		},
			            		{
			            			"id":"nominal",
				            		"label":"nominal",
				            		"type":"text"
			            		}
		            		];
		                },
	                "data": resp.potongan
		          });
			$(".div_potongan").find('.nominal').trigger('change');
			},'json');
	}

  	$('.div_terima').on('change', '.nominal', function() {
  		jml_income();
    });

    $('.div_potongan').on('change', '.nominal', function() {
  		jml_potongan();
    });

    function jml_income() {
    	let nominal_terima = 0;
    	$('.div_terima').find('.row_div_terima').each(function(){
    		let qty 	= parseInt($(this).closest('tr').find('.qty').val());
    		console.log(jml_hari+"hari");
    		if (jml_hari>0 && qty>1) {
      			qty = jml_hari;
      			$(this).closest('tr').find('.qty').val(qty);
      		}
    		let total 	= parseFloat($(this).closest('tr').find('.nominal').val())*qty;
      		nominal_terima += total;
      		console.log(qty+'-'+total);
      	});
      	console.log(nominal_terima);
      	$("#txt_total_terima").text(parseFloat(nominal_terima, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
      	$("#gaji_brutto").val(nominal_terima);
    }

    function jml_potongan() {
    	let nominal_potong = 0;
    	$('.div_potongan').find('.nominal').each(function(){
      		if ($.isNumeric($(this).val())) {
      			nominal_potong += parseFloat($(this).val());
      		}
      	});
      	console.log(nominal_potong);
      	// $("#txt_total_potong").text(nominal_potong);
      	// $("#txt_total_potong").inputmask('IDR');
      	$("#txt_total_potong").text(parseFloat(nominal_potong, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
      	$("#gaji_potongan").val(nominal_potong);
    }
  <?=$this->config->item('footerJS')?>
</script>