<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Absensi Pegawai')?>
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
          <h3 class="box-title">Form Absensi Pegawai</h3>
          <div class="box-tools pull-right">
            <button type="button" id="btn-add" class="btn btn-primary">
              <i class="fa fa-plus"></i> Add</button>
          </div>
        </div>
        <div class="box-body" id="form_absensi_pegawai" style="display: none;">
        </div>
        <div class="box-body" id="data_absensi_pegawai">
          <?=create_inputDaterange("filter_tanggal_import",["locale"=>["format"=>"YYYY-MM-DD","separator"=>"/"]])?>
          <?=create_table("tb_absensi_pegawai","M_absensi_pegawai",["class"=>"table table-bordered" ,"style" => "width:100% !important;"])?>
        </div>
        <div class="box-footer">
          <button class="btn btn-danger" id="btn-deleteChecked"><i class="fa fa-trash"></i> Delete</button>
          <button class="btn btn-success" id="btn-import" onclick="import_data()"><i class="fa fa-file-excel-o"></i>Import</button>
          <!-- <div class="pull-right">
            <form method="post" enctype="multipart/form-data" action="absensi_pegawai/go_import">
            <div class="col-md-6">
              <input type="file" name="file_excel" id="file_excel">
            </div>
            <div class="col-md-4">
              <button class="btn btn-success" id="btn-import"><i class="fa fa-file-excel-o"></i> Import</button>  
            </div>
            </form>
          </div> -->
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?=modal_open("modal_import","Import Absensi Pegawai")?>
<?=modal_close()?>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
        table = $('#tb_absensi_pegawai').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('absensi_pegawai/get_data')?>",
                "type": "POST",
                "data": function(f) {
                    f.tanggal = $("#filter_tanggal_import").val();
                }
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

        $("#filter_tanggal_import").change(function(){
          table.draw();
        });
    });
    $("#btn-add").click(function() {
      $("#form_absensi_pegawai").show();
      $("#form_absensi_pegawai").load("absensi_pegawai/show_form");
    });
    function set_val(id) {
      $("#form_absensi_pegawai").show();
      $.get('absensi_pegawai/find_one/'+id,(data)=>{
          $("#form_absensi_pegawai").load("absensi_pegawai/show_form",()=>{
            $.each(data,(ind,obj)=>{
                $("#"+ind).val(obj);
            });
          });
      },'json');
    }

    function deleteRow(id) {
      if (confirm("Anda yakin akan menghapus data ini?")) {
          $.get('absensi_pegawai/delete_row/'+id,(data)=>{
            alert(data.message);
            location.reload();
        },'json');
      }
    }

    function import_data() {
      $("#modal_import").modal('show');
      $("#modal_import").find('.modal-body').load("absensi_pegawai/form_import");
    }

    $("#checkAll").click(()=>{
      if ($("#checkAll").is(':checked')) {
          $("#tb_absensi_pegawai input[type='checkbox']").attr("checked",true);
      }else{
          $("#tb_absensi_pegawai input[type='checkbox']").attr("checked",false);
      }
    });

    $("#btn-deleteChecked").click(function(event){
        event.preventDefault();
        var searchIDs = $("#tb_absensi_pegawai input:checkbox:checked").map(function(){
              return $(this).val();
          }).toArray();
        if (searchIDs.length == 0) {
          alert("Mohon cek list data yang akan dihapus");
          return false;
        }
        if (confirm("Anda yakin akan menghapus data ini?")) {
          $.post('absensi_pegawai/delete_multi',{data:searchIDs},(resp)=>{
            alert(resp.message);
            location.reload();
          },'json');
        }
    });
    <?=$this->config->item('footerJS')?>
</script>