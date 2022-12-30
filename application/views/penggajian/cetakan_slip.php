<style>
    img {
        width: 70%;
        height: auto;
        opacity: 0.5;
    }
    @page {
        margin: 5px; /* <any of the usual CSS values for margins> */
                    /*(% of page-box width for LR, of height for TB) */
					page-break-after: always;
    }
	body{font-size: 110%};
	.tabelku {
		border-top:    1px solid  black;
		border-right:  1px solid  black;
		border-bottom: 1px solid  black;
		border-left:   1px solid  black;
	}
	.borderatas {
		border-top:    1px solid  black;
	}
	table {
		border-collapse: collapse;
	}
</style>
<page>
<table style="width: 100%;" width="100%" class="tabelku">
	<tbody>
		<tr>
			<td style="width:70%" colspan="2">
                <h4 style=" text-decoration: underline black;"><?=$instansi->nama_profil?></h4>
            </td>
            <td style="text-align: right; padding-right:30px">
                <img src="<?=base_url($instansi->logo_profil)?>" alt="">
            </td>
		</tr>
		<tr>
			<td colspan="2">
				<table style="width: 100%;">
					<tbody>
						<tr>
							<td style="width: 90%; font-size:15px;" colspan="2">Slip Gaji Periode : <?=get_namaBulan($header_gaji->gaji_month)."(".date('d-M-Y',strtotime($header_gaji->gaji_date_start)).'/'.date('d-M-Y',strtotime($header_gaji->gaji_date_end)).")"?></td>
							<td style="width: 305.396px;">Gaji No</td>
							<td style="width: 305.396px; font-weight: bold;"><?=$header_gaji->gaji_no?></td>
						</tr>
						<tr>
							<td style="width: 153px;">Nama Pegawai</td>
							<td style="width: 17px;">: <?=$pegawai->emp_name.'('.$pegawai->emp_no.')'?></td>
							<td style="width: 305.396px;" colspan="2">
                                Hari : <?=$header_gaji->date_qty?>
                            </td>
						</tr>
						<?php
							foreach ($list_gaji as $key => $value) {
								echo "<tr>
									<td>Gaji ".($key+1)."</td>
									<td>: ".convert_currency($value->gaji)."</td>
									<td></td>
								</tr>";
							}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td style="width: 50%;" class="borderatas"><b>Penerimaan :</b></td>
			<td style="width: 50%;" class="borderatas"><b>Potongan :</b></td>
		</tr>
		<tr>
			<td style="width: 50%;">
				<table>
					<tbody>
                        <?php
                            foreach ($penerimaan as $key => $value) {
                                echo "<tr>
                                        <td>$value->gaji_income_note</td>
                                        <td>:</td>
                                        <td>".convert_currency($value->gaji_income_nominal)."</td>
                                </tr>";
                            }
                        ?>
						<tr>
							<td class="borderatas">Total Penerimaan</td>
							<td class="borderatas">:</td>
							<td class="borderatas" style="font-weight: bold;"><?=convert_currency($header_gaji->gaji_brutto)?></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td style="width: 244.635px; height: 41px;">
				<table style="width: 100%;">
					<tbody>
                    <?php
                            foreach ($potongan as $key => $value) {
                                echo "<tr>
                                        <td>$value->gaji_potongan_note</td>
                                        <td>:</td>
                                        <td>".convert_currency($value->gaji_potongan_nominal)."</td>
                                </tr>";
                            }
                        ?>
						<tr>
							<td class="borderatas">Total Potongan</td>
							<td class="borderatas">:</td>
							<td class="borderatas" style="font-weight: bold;"><?=convert_currency($header_gaji->gaji_potongan)?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr style="height: 41px;">
			<td></td>
			<td style="width: 488.5px; height: 41px;">
				<table style="height: 17px; width: 475px;">
					<tbody>
						<tr>
							<td style="width: 138.771px; font-weight: bold;" class="borderatas">Netto</td>
							<td style="width: 333.229px; font-weight: bold;" class="borderatas"><?=convert_currency($header_gaji->gaji_netto)?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</page>
<!-- DivTable.com -->