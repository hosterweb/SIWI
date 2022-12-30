<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Pelaporan Sistem')?>
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
        <div class="box-header with-border">
          <h3 class="box-title">Laporan Data Pegawai</h3>
        </div>
        <div class="box-body">
        <?=form_open("laporan_data_pegawai/data",["target"=>"_blank","method"=>"post","id"=>"form_laporan_pegawai"])?>
            <?=create_select([
							"attr" =>["name"=>"unit_id=department","id"=>"unit_id","class"=>"form-control","required"=>"true"],
							"model"=>["m_ms_department" => ["get_ms_department",["department_active"=>'t']],
											"column"  => ["department_id","nama_deparment"]
										],
						])?>
            <?=create_select([
							"attr"=>["name"=>"emp_type=Tipe Pegawai","id"=>"emp_type","class"=>"form-control"],
							"option"=> [["id"=>'1',"text"=>"Non Shift"],["id"=>'2',"text"=>"Shift"]],
						])?>
            <?=create_select([
							"attr" =>["name"=>"position_id=jabatan","id"=>"position_id","class"=>"form-control","required"=>"true"],
							"model"=>["m_ms_jabatan" => ["get_jabatan",["jabatan_aktif"=>'t']],
											"column"  => ["id_jabatan","nama_jabatan"]
										],
						])?>
        </div>
        <div class="box-footer">
          <button class="btn btn-info" id="btn-data"><i class="fa fa-eye"></i> Tampilkan</button>
        <?=form_close()?>
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
        table = $('#tb_ms_cuti').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('ms_cuti/get_data')?>",
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
    $("#btn-add").click(function() {
      $("#form_ms_cuti").show();
      $("#form_ms_cuti").load("ms_cuti/show_form");
    });
    function set_val(id) {
      $("#form_ms_cuti").show();
      $.get('ms_cuti/find_one/'+id,(data)=>{
          $("#form_ms_cuti").load("ms_cuti/show_form",()=>{
            $.each(data,(ind,obj)=>{
                $("#"+ind).val(obj);
            });
          });
      },'json');
    }

    function deleteRow(id) {
      if (confirm("Anda yakin akan menghapus data ini?")) {
          $.get('ms_cuti/delete_row/'+id,(data)=>{
            alert(data.message);
            location.reload();
        },'json');
      }
    }

    $("#checkAll").click(()=>{
      if ($("#checkAll").is(':checked')) {
          $("#tb_ms_cuti input[type='checkbox']").attr("checked",true);
      }else{
          $("#tb_ms_cuti input[type='checkbox']").attr("checked",false);
      }
    });

    $("#btn-deleteChecked").click(function(event){
        event.preventDefault();
        var searchIDs = $("#tb_ms_cuti input:checkbox:checked").map(function(){
              return $(this).val();
          }).toArray();
        if (searchIDs.length == 0) {
          alert("Mohon cek list data yang akan dihapus");
          return false;
        }
        if (confirm("Anda yakin akan menghapus data ini?")) {
          $.post('ms_cuti/delete_multi',{data:searchIDs},(resp)=>{
            alert(resp.message);
            location.reload();
          },'json');
        }
    });
</script>