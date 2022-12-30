<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Employee')?>
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
          <h3 class="box-title">Form Employee</h3>
          <div class="box-tools pull-right">
            <button type="button" id="btn-add" class="btn btn-primary">
              <i class="fa fa-plus"></i> Add</button>
          </div>
        </div>
        <div class="box-body" id="form_employee" style="display: none;">
        </div>
        <div class="box-body" id="data_employee">
          <?=create_table("tb_employee","M_employee",["class"=>"table table-bordered" ,"style" => "width:100% !important;"])?>
        </div>
        <div class="box-footer">
          <button class="btn btn-danger" id="btn-deleteChecked"><i class="fa fa-trash"></i> Delete</button>
          <div class="pull-right">
            <form method="post" enctype="multipart/form-data" id="form_importan" action="employee/go_import">
            <div class="col-md-6">
              <input type="file" name="file_excel" id="file_excel">
            </div>
            <div class="col-md-4">
              <button class="btn btn-success" id="btn-import"><i class="fa fa-file-excel-o"></i> Import</button>  
            </div>
            </form>
          </div>
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">
    var table,image_profil;
    $(document).ready(function() {
        table = $('#tb_employee').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'print',
                'excelHtml5',
                'pdfHtml5'
            ], 
            "processing": true, 
            "serverSide": true,
            "pageLength": 10, 
            "order": [], 
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('employee/get_data')?>",
                "type": "POST"
            },
            'columnDefs': [
            {
              'targets': [0,1,2,-1],
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

    $("#form_importan").submit(function(){
        if (!$("#file_excel").val()) {
          alert("Mohon upload file yang sesuai");
          return false;
        }
    });
    $("#btn-add").click(function() {
      $("#form_employee").show();
      image_profil="";
      $("#data_employee, #btn-deleteChecked").hide();
      $("#form_employee").load("employee/show_form");
    });
    function set_val(id) {
      $("#form_employee").show();
      $.get('employee/find_one/'+id,(data)=>{
          $("#form_employee").load("employee/show_form",()=>{
            $.each(data,(ind,obj)=>{
                $("#"+ind).val(obj);
                if (ind == 'emp_photo') {
                  image_profil = obj;
                }
            });
            $.when(get_reg('emp_city',data.emp_prov)).done(
              setTimeout(function(){
                $("#emp_city").val(data.emp_city);
                $.when(get_reg('emp_resident',data.emp_city)).done(
                  setTimeout(function(){
                    $("#emp_resident").val(data.emp_resident);
                    $.when(get_reg('emp_district',data.emp_resident)).done(
                      setTimeout(function(){
                        $("#emp_district").val(data.emp_district);
                      }),500)
                  },500)
                )
              },500)
            )
          });
          
      },'json');
    }

    function deleteRow(id) {
      if (confirm("Anda yakin akan menghapus data ini?")) {
          $.get('employee/delete_row/'+id,(data)=>{
            alert(data.message);
            location.reload();
        },'json');
      }
    }

    $("#checkAll").click(()=>{
      if ($("#checkAll").is(':checked')) {
          $("#tb_employee input[type='checkbox']").attr("checked",true);
      }else{
          $("#tb_employee input[type='checkbox']").attr("checked",false);
      }
    });

    $("#btn-deleteChecked").click(function(event){
        event.preventDefault();
        var searchIDs = $("#tb_employee input:checkbox:checked").map(function(){
              return $(this).val();
          }).toArray();
        if (searchIDs.length == 0) {
          alert("Mohon cek list data yang akan dihapus");
          return false;
        }
        if (confirm("Anda yakin akan menghapus data ini?")) {
          $.post('employee/delete_multi',{data:searchIDs},(resp)=>{
            alert(resp.message);
            location.reload();
          },'json');
        }
    });
</script>