<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Jadwal Karyawanshift')?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Layout</a></li>
        <li class="active">Fixed</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="box">
        <?=$this->session->flashdata('message')?>
        <div class="box-header with-border">
          <h3 class="box-title">Form Jadwal Karyawanshift</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-plus"></i> Add
              <span class="fa fa-caret-down"></span></button>
            <ul class="dropdown-menu">
              <li><a href="#" id="btn-add-perpegawai">Per Pegawai</a></li>
              <li><a href="#" id="btn-add-perdepartment">Per Department</a></li>
            </ul>
          </div>
        </div>
        <div class="box-body" id="form_jadwal_karyawanshift" style="display: none;">
        </div>
        <div class="box-body" id="data_jadwal_karyawanshift">
          <?=create_table("tb_jadwal_karyawanshift","M_jadwal_karyawanshift",["class"=>"table table-bordered" ,"style" => "width:100% !important;"])?>
        </div>
        <div class="box-footer" id="footer-index">
          <button class="btn btn-danger" id="btn-deleteChecked"><i class="fa fa-trash"></i> Delete</button>
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">
    var table;
    $(document).ready(function() {
        table = $('#tb_jadwal_karyawanshift').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('jadwal_karyawanshift/get_data')?>",
                "type": "POST"
            },
            'columnDefs': [
            {
              'targets': [0,1,-1],
               'searchable': false,
               'orderable': false,
             },
            {
               'targets': 0,
               'className': 'dt-body-center',
               'render': function (data, type, full, meta){
                   return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
               }
            }], 
        });
    });
    $("#btn-add-perpegawai").click(function() {
      $("#data_jadwal_karyawanshift, #footer-index").hide();
      $("#form_jadwal_karyawanshift").show();
      $("#form_jadwal_karyawanshift").load("jadwal_karyawanshift/show_form_multi_emp");
    });
    function set_val(id) {
      $("#form_jadwal_karyawanshift").show();
      $.get('jadwal_karyawanshift/find_one/'+id,(data)=>{
          $("#form_jadwal_karyawanshift").load("jadwal_karyawanshift/show_form",()=>{
            $.each(data,(ind,obj)=>{
                $("#"+ind).val(obj);
                if(ind=='emp_type' && obj == 2){
                    $("#karyawan_shift").attr("checked",true);
                    $('#karyawan_shift').bootstrapSwitch('state', true);
                }else{
                  $('#karyawan_shift').bootstrapSwitch('state', true);
                }
            });
          });
      },'json');
    }

    function deleteRow(id) {
      if (confirm("Anda yakin akan menghapus data ini?")) {
          $.get('jadwal_karyawanshift/delete_row/'+id,(data)=>{
            alert(data.message);
            location.reload();
        },'json');
      }
    }

    $("#checkAll").click(()=>{
      if ($("#checkAll").is(':checked')) {
          $("#tb_jadwal_karyawanshift input[type='checkbox']").attr("checked",true);
      }else{
          $("#tb_jadwal_karyawanshift input[type='checkbox']").attr("checked",false);
      }
    });

    $("#btn-deleteChecked").click(function(event){
        event.preventDefault();
        var searchIDs = $("#tb_jadwal_karyawanshift input:checkbox:checked").map(function(){
              return $(this).val();
          }).toArray();
        if (searchIDs.length == 0) {
          alert("Mohon cek list data yang akan dihapus");
          return false;
        }
        if (confirm("Anda yakin akan menghapus data ini?")) {
          $.post('jadwal_karyawanshift/delete_multi',{data:searchIDs},(resp)=>{
            alert(resp.message);
            location.reload();
          },'json');
        }
    });
</script>