    <div class="col-md-12">
			<?=form_open("jadwal_karyawanshift/save",["method"=>"post","id"=>"fm_jadwal_karyawanshift"],$model)?>
      <div class="row">
        <div class="col-sm-3">
            <?=create_switchbutton("karyawan_shift",[
                  "onText"      => "YA",
                  "offText"       => "TIDAK",
                  "onSwitchChange"  => "function(){
                    if($(this).is(':checked')){
                      $(this).val(2);
                      $('#tanggal').attr('disabled',false);
                    }else{
                      $(this).val(1);
                      $('#tanggal').attr('disabled',true);
                    }
                    $('#unit_id').trigger('change');
                  }"
                ])?>
        </div>
        <div class="col-sm-4">
          <?=create_inputDate("tanggal",["format"=>"yyyy-mm-dd","autoclose"=>true],["disabled"=>true])?>
        </div>
        <div class="col-sm-5">
            <?=create_select2([
              "attr" =>["name"=>"unit_id=department","id"=>"unit_id","class"=>"form-control select2"],
              "model"=>["m_ms_department" => ["get_ms_department",["department_active"=>'t']],
                      "column"  => ["department_id","nama_deparment"]
                    ],
            ])?>
        </div>
      </div>
      <div id="div_multi" class="div_multi"></div>
      <?=form_close()?>
      <div class="box-footer text-center">
      		<button class="btn btn-primary" type="button" onclick="$('#fm_jadwal_karyawanshift').submit()">Save</button>
      		<button class="btn btn-warning" type="button" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
  $(document).ready(()=>{
    $("#karyawan_shift").val(1);
    $("#div_multi").inputMultiRow({
            column: ()=>{
                var dataku;
                $.ajax({
                    'async': false,
                    'type': "GET",
                    'dataType': 'json',
                    'url': "jadwal_karyawanshift/get_multiRows",
                    'success': function (data) {
                        dataku = data;
                    }
                });
                return dataku;
                }
          });
  });

  $("#unit_id").change(function(){
    $.get("jadwal_karyawanshift/get_emp_by_dep/"+$(this).val()+"/"+$("#karyawan_shift").val()+"/"+$("#tanggal").val(),function(obj){
      $("#div_multi").inputMultiRow({
                      column: ()=>{
                                var dataku;
                                $.ajax({
                                    'async': false,
                                    'type': "GET",
                                    'dataType': 'json',
                                    'url': "jadwal_karyawanshift/get_multiRows",
                                    'success': function (data) {
                                        dataku = data;
                                    }
                                });
                                return dataku;
                              },
                      "data": obj
                    });
    },'json')
  });

	$("#btn-cancel").click( () => {
		$("#form_jadwal_karyawanshift").hide();
		$("#form_jadwal_karyawanshift").html('');
    $("#data_jadwal_karyawanshift,#footer-index").show();
	});
	$("body").on("focus", ".autocom_emp_id", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('jadwal_karyawanshift/get_employee/name');?>/"+$("#unit_id").val()+"/"+$("#karyawan_shift").val(),
            select: function (event, ui) {
                $(this).closest('tr').find('.emp_id').val(ui.item.emp_id);
                $(this).closest('tr').find('.employee_code').val(ui.item.emp_no);
            }
        });
	});
	$("body").on("focus", ".employee_code", function() {
	    $(this).autocomplete({
        source: "<?php echo site_url('jadwal_karyawanshift/get_employee/code');?>/"+$("#unit_id").val()+"/"+$("#karyawan_shift").val(),
            select: function (event, ui) {
                $(this).closest('tr').find('.emp_id').val(ui.item.emp_id);
                $(this).closest('tr').find('.autocom_emp_id').val(ui.item.emp_name);
            }
        });
	});
  <?=$this->config->item('footerJS')?>
</script>