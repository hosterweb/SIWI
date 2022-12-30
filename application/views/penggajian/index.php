<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=ucwords('Penggajian')?>
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
          <h3 class="box-title">Form Penggajian</h3>
          <div class="box-tools pull-right">
            <button type="button" id="btn-add" class="btn btn-primary">
              <i class="fa fa-plus"></i> Add</button>
          </div>
        </div>
        <div class="box-body" id="form_penggajian" style="display: none;">
        </div>
        <div class="box-body" id="data_penggajian">
          <?=create_table("tb_penggajian","M_penggajian",["class"=>"table table-bordered" ,"style" => "width:100% !important;"])?>
        </div>
        <div class="box-footer">
          <button class="btn btn-danger" id="btn-deleteChecked"><i class="fa fa-trash"></i> Delete</button>
          <button class="btn btn-warning" id="btn-generateGaji" onclick="input_multi_gaji()"><i class="fa fa-gear"></i> Generate Gaji</button>
          <button class="btn btn-info" id="btn-copyGaji" onclick="copy_gaji()"><i class="fa fa-copy"></i> Copy Gaji</button>
          <button class="btn btn-success" id="btn-cetak" onclick="cetak_multi()"><i class="fa fa-print"></i> Cetak Slip Bulanan</button>
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?=modal_open("modal_gaji","Generate Gaji Karyawan")?>
<?=modal_close()?>
<?=modal_open("modal_cetak","Cetak Slip Gaji Karyawan")?>
<?=modal_close()?>
<script type="text/javascript">
    var table;
    $(document).ready(function() {
        table = $('#tb_penggajian').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "scrollX": true,
            "ajax": {
                "url": "<?php echo site_url('penggajian/get_data')?>",
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
      $("#form_penggajian").show();
      $("#form_penggajian").load("penggajian/show_form");
    });
    function set_val(id) {
      $("#form_penggajian").show();
      $.get('penggajian/find_one/'+id,(data)=>{
          $("#form_penggajian").load("penggajian/show_form",()=>{
            $.each(data,(ind,obj)=>{
                $("#"+ind).val(obj);
            });
          });
      },'json');
    }

    function input_multi_gaji() {
      $("#modal_gaji").modal('show');
      $("#modal_gaji").find('.modal-body').load("penggajian/form_multi");
    }

    function copy_gaji() {
      $("#modal_gaji").modal('show');
      $("#modal_gaji").find('.modal-body').load("penggajian/form_copy");
    }

    function cetak_multi() {
      $("#modal_cetak").modal('show');
      $("#modal_cetak").find('.modal-body').load("penggajian/form_cetak");
    }

    function cetak_slip(id) {
      var myWindow=window.open('<?php echo base_url()?>penggajian/cetak_slip/'+id,'','width=800,height=500');     
	    myWindow.focus();
    }

    function deleteRow(id) {
      if (confirm("Anda yakin akan menghapus data ini?")) {
          $.get('penggajian/delete_row/'+id,(data)=>{
            alert(data.message);
            location.reload();
        },'json');
      }
    }

    $("#checkAll").click(()=>{
      if ($("#checkAll").is(':checked')) {
          $("#tb_penggajian input[type='checkbox']").attr("checked",true);
      }else{
          $("#tb_penggajian input[type='checkbox']").attr("checked",false);
      }
    });

    $("#btn-deleteChecked").click(function(event){
        event.preventDefault();
        var searchIDs = $("#tb_penggajian input:checkbox:checked").map(function(){
              return $(this).val();
          }).toArray();
        if (searchIDs.length == 0) {
          alert("Mohon cek list data yang akan dihapus");
          return false;
        }
        if (confirm("Anda yakin akan menghapus data ini?")) {
          $.post('penggajian/delete_multi',{data:searchIDs},(resp)=>{
            alert(resp.message);
            location.reload();
          },'json');
        }
    });
</script>