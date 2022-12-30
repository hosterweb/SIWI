<div class="col-md-12">
      		<?=form_open("employee/save",["method"=>"post","id"=>"form_inputEmployee","enctype"=>"multipart/form-data"],$model)?>
			<?=form_hidden("emp_id")?>
			<div class="row">
				<?=form_fieldset('Biodata Information');?>
					<div class="col-sm-3">
						<?=create_input("emp_no=No Pegawai")?>
						<?=create_input("emp_npwp=NPWP")?>
						<?=create_input("emp_noktp=No KTP")?>
						<?=create_input("emp_nokk=No KK")?>
						<?=create_input("emp_name=Nama")?>
					</div>
					<div class="col-sm-3">
						<?=create_select([
							"attr"=>["name"=>"emp_sex=Jenis Kelamin","id"=>"emp_sex","class"=>"form-control"],
							"option"=>[
								["id"=>"L","text"=>"Laki-laki"],["id"=>"P","text"=>"Perempuan"]
									]
						])?>
						<?=create_inputDate("emp_birthdate=Tanggal Lahir",["format"=>"yyyy-mm-dd"])?>
						<?=create_input("emp_phone=No Telp")?>
						<?=create_input("emp_mail=E-mail")?>
						<?=create_select([
							"attr"=>["name"=>"pendidikan=Pendidikan Terakhir","id"=>"pendidikan","class"=>"form-control"],
							"option"=> get_pendidikan(),
						])?>
						<?=create_input("jurusan_pendidikan")?>
						<?=create_input("anak=jumlah anak")?>
					</div>
					<div class="col-sm-3">
						<?=create_select([
							"attr"=>["name"=>"emp_status=Status Perkawinan","id"=>"emp_status","class"=>"form-control"],
							"option"=> get_status(),
						])?>
						<?=create_input("emp_couple=Nama Pasangan")?>
						<?=create_input("emp_born=Tempat lahir")?>
						<?=create_select([
							"attr"=>["name"=>"agama","id"=>"agama","class"=>"form-control"],
							"option"=> get_agama(),
						])?>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
				            <div class="file-loading">
				                <input type="file" name="file_photo" id="file_photo" data-upload-url="#">
				            </div>
				            <div id="errorBlock" class="help-block"></div>
				        </div>
					</div>
				<?=form_fieldset_close();?>
				<?=form_fieldset('Work Information');?>
					<div class="col-sm-6">
						<?=create_inputDate("tahun_masuk",["format"=>"yyyy-mm-dd"])?>
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
						<?=create_input("no_bpjs_kesehatan")?>
						<?=create_input("no_bpjs_ketenagakerjaan")?>
					</div>
					<div class="col-sm-6">
						<?=create_select([
							"attr" =>["name"=>"position_id=jabatan","id"=>"position_id","class"=>"form-control","required"=>"true"],
							"model"=>["m_ms_jabatan" => ["get_jabatan",["jabatan_aktif"=>'t']],
											"column"  => ["id_jabatan","nama_jabatan"]
										],
						])?>
						<?=create_select([
							"attr"=>["name"=>"emp_active=Status","id"=>"emp_active","class"=>"form-control"],
							"option"=> [["id"=>'t',"text"=>"Aktif"],["id"=>'f',"text"=>"Non Aktif"]],
						])?>
						<?=create_select([
							"attr"=>["name"=>"penggajian","id"=>"penggajian","class"=>"form-control"],
							"option"=> ["harian","bulanan"],
						])?>
						<?=create_input("no_kpa")?>
						<?=create_input("absen_code=ID Checklog Pegawai")?>
					</div>
					<div class="col-sm-12">
					<?=create_textarea("catatan")?>
					</div>
				<?=form_fieldset_close();?>
				<?=form_fieldset('Address Information');?>
					<div class="col-sm-6">
						<?=create_select([
							"attr" =>["name"=>"emp_prov=provinsi","id"=>"emp_prov","class"=>"form-control","onchange"=>"get_reg('emp_city',this.value)"],
							"model"=>["m_ms_region" => ["get_ms_region",["reg_level"=>'1']],
											"column"  => ["reg_code","reg_name"]
										],
						])?>
						<?=create_select([
							"attr" =>["name"=>"emp_city=kabupaten","id"=>"emp_city","class"=>"form-control","onchange"=>"get_reg('emp_resident',this.value)"],
						])?>
					</div>
					<div class="col-sm-6">
						<?=create_select([
							"attr" =>["name"=>"emp_resident=kecamatan","id"=>"emp_resident","class"=>"form-control","onchange"=>"get_reg('emp_district',this.value)"],
						])?>
						<?=create_select([
							"attr" =>["name"=>"emp_district=desa","id"=>"emp_district","class"=>"form-control"],
						])?>
					</div>
					<div class="col-sm-12">
						<?=create_textarea("emp_address=Alamat")?>
						<?=create_textarea("alamat_domisili")?>
					</div>
				<?=form_fieldset_close();?>
			</div>
<?=form_close()?>
      <div class="box-footer">
      		<button class="btn btn-primary" onclick="$('#form_inputEmployee').submit()">Save</button>
      		<button class="btn btn-warning" id="btn-cancel">Cancel</button>
      </div>
    </div>
<script type="text/javascript">
	$("#btn-cancel").click( () => {
		$("#data_employee, #btn-deleteChecked").show();
		$("#form_employee").hide();
		$("#form_employee").html('');
		image_profil="";
	});
	$(document).ready(function() {
		$("#file_photo").fileinput({
	        showCaption: true,
	        showRemove: true,
	        showUpload: false,
	        allowedFileTypes: ["image"],
	        initialPreview: [
			    "<img src='<?=base_url("assets/uploads/photo_pegawai/")?>"+image_profil+"' class='file-preview-image' alt='Foto' title='Foto'>"
			],
	        "fileActionSettings" : {
				"showRemove": true,
				"showDrag": false,
				"showZoom": true,
				"showUpload": false,
				"indicatorNew": "",
				"indicatorSuccess": "",
				"indicatorError": ""
			},
	    });
     });
	
	function get_reg(dest,id) {
		$("#"+dest+" > option").remove();
		const setData = $.get("employee/get_region/"+id,(resp)=>{
							$("#"+dest+"").append(resp);
						});
		return setData;
	}

	<?=$this->config->item('footerJS')?>
</script>