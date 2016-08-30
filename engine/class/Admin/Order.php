<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminOrder
 *
 * @author tomi_weber
 */
class Admin_Order {

	protected $o_id;

	protected $user_panel;

	protected $o_name;

	protected $o_adress1;

	protected $o_adress2;

	protected $o_zip;

	protected $o_city;

	protected $o_country;

	protected $o_nip;

	protected $o_phone;

	protected $o_email;

	protected $o_fee;

	protected $o_payment_status;

	protected $transport_id;

	protected $o_supply_number;

	protected $o_comments;

	protected $o_datetime;

	protected $payment_id;

	protected $o_items;

	protected $o_sum;

	protected $o_status;

	protected $control;

	protected $temp_order_id;

	public function __construct($oId = 0) {

		if ($oId > 0){
			try {

				$sql = "SELECT *
						FROM shop_order
						WHERE o_id = ".$oId."
						";
				$tabelaWynikow = ConnectDB::subQuery($sql);
				if(count($tabelaWynikow) == 0){
					return false;
				}

				foreach ($tabelaWynikow as $row)
				{
					$this -> o_id				= $row['o_id'];
					$this -> user_panel			= $row['user_panel'];
					$this -> o_name				= $row['o_customer_name'];
					$this -> o_surname			= $row['o_customer_surname'];
					$this -> o_street			= $row['o_street'];
					$this -> o_house			= $row['o_house_number'];
					$this -> o_room				= $row['o_room_number'];
					$this -> o_zip				= $row['o_zip'];
					$this -> o_city				= $row['o_city'];
					$this -> o_country			= $row['o_country'];
					$this -> o_nip				= $row['o_nip'];
					$this -> o_phone			= $row['o_phone'];
					$this -> o_email				= $row['o_email'];
					$this -> o_fee				= $row['o_fee'];
					$this -> devilery_id			= $row['devilery_id'];
					$this -> o_supply_number		= $row['o_supply_number'];
					$this -> o_comments			= $row['o_comments'];
					$this -> o_datetime			= $row['o_datetime'];
					$this -> o_items				= $row['o_items'];
					$this -> o_sum				= $row['o_sum'];
					$this -> status_id			= $row['status_id'];
					$this -> control			= $row['control'];

				}
	
			}catch (PDOException $e){
				//echo "Błąd nie można utworzyć obiektu material.";
				return false;
			}
		} else {
			$this -> o_id				= 0;
			$this -> user_panel			= 0;
			$this -> o_name				= '';
			$this -> o_surname			= '';
			$this -> o_street			= '';
			$this -> o_house			= '';
			$this -> o_room				= '';
			$this -> o_zip				= '';
			$this -> o_city				= '';
			$this -> o_country			= 0;
			$this -> o_nip				= '';
			$this -> o_phone			= '';
			$this -> o_email				= '';
			$this -> o_fee				= 0;
			$this -> devilery_id			= 0;
			$this -> o_supply_number		= '';
			$this -> o_comments			= '';
			$this -> o_datetime			= date('Y-m-d H-m-s');
			$this -> o_items				= 0;
			$this -> o_sum				= 0;
			$this -> status_id			= 0;
			$this -> control			= '';
		}
	}

	//gety
	public function getUserPanel() {
		return $this -> user_panel ;
	}

	public function getCustomerName() {
		return $this -> o_name ;
	}
	
	public function getCustomerSurName() {
		return $this -> o_surname ;
	}

	public function getStreet() {
		return $this -> o_street ;
	}

	public function getHouse() {
		return $this -> o_house ;
	}
	
	public function getRoom() {
		return $this -> o_room ;
	}

	public function getZip() {
		return $this -> o_zip ;
	}

	public function getCity() {
		return $this -> o_city ;
	}

	public function getNIP() {
		return $this -> o_nip ;
	}

	public function getPhone() {
		return $this -> o_phone ;
	}

	public function getEmail() {
		return $this -> o_email ;
	}

	public function getFee() {
		return $this -> o_fee ;
	}

	public function getPaymentStatus() {
		return $this -> o_payment_status ;
	}

	public function getDevileryID() {
		return $this -> devilery_id ;
	}

	public function getSupplyID() {
		return $this -> o_supply_number ;
	}

	public function getComments() {
		return $this -> o_comments ;
	}

	public function getItems() {
		return $this -> o_items ;
	}

	public function getSum() {
		return $this -> o_sum ;
	}

	public function getStatus() {
		return $this -> status_id ;
	}

	public function getControl() {
		return $this -> control ;
	}
	
	public function setDevileryID($dev_id) {
		$this -> devilery_id = $dev_id ;
		return $this;
	}
	
	public function setTempOrderID($temp_id) {
		$this -> temp_order_id = $temp_id ;
		return $this;
	}
	
	public function setID($id) {
		$this -> id = $id ;
		return $this;
	}

	public function calcFee() {
		$obOrder = new Order();
		$aDevilery = $obOrder -> getDevilery();
		$suma = 0;

		foreach($aDevilery as $row_d) {
			if($row_d['devilery_id'] == $this -> devilery_id) {
				$suma += $row_d['devilery_price'];
			}
		}

		return $suma;
	}

	public function saveOrder($aData) {
		
		$lastId = ConnectDB::subAutoExec ("shop_order", $aData, "INSERT");
		$this -> setID($lastId);
		
		$aResult = $this -> getTempItems($this -> temp_order_id); 
		$sum = 0; $items = 0;
		
		foreach($aResult as $row) {
			
			$aDataItems = array();
			
			if($row['p_promo_price'] == 0)
				$aDataItems['i_price'] = $row['p_price_gross'];
			else
				$aDataItems['i_price'] = $row['p_promo_price'];
			
			$aDataItems['o_id'] = $lastId;
			$aDataItems['i_name'] = $row['p_name'];
			$aDataItems['i_pieces'] = $row['items'];
			$aDataItems['i_sum'] = $aDataItems['i_price']*$row['items'];
			$aDataItems['p_id'] = $row['p_id'];

			$sum += $aDataItems['i_price'] *$row['items'];
			$items += $row['items'];
			
			ConnectDB::subAutoExec ("shop_order_items", $aDataItems, "INSERT");
			$aDataItems = null;

		}
		
		$sql = "UPDATE shop_order SET o_items = ".$items.", o_sum = ".($sum+$aData['o_fee'])." WHERE o_id = ".$lastId;
		ConnectDB::subExec($sql);

		$sql = "DELETE FROM shop_temp_order WHERE temp_id = ".$this->temp_order_id;
		ConnectDB::subExec($sql);

		$sql = "DELETE FROM shop_temp_order_items WHERE order_id = ".$this->temp_order_id;
		ConnectDB::subExec($sql);

		return $lastId;
	}

	public function getTempItems($order_id) {

		$sql = "SELECT * FROM shop_temp_order_items JOIN shop_product USING(p_id) WHERE order_id = ".$order_id;
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function addTempOrderAjax() {

		$sql = "INSERT INTO shop_temp_order (order_date) VALUES (now())";
		$lastId = ConnectDB::subExec($sql);
		return $lastId;
	}

	public function addTempItemAjax($id_order, $p_id, $items) {

		$sql = "INSERT INTO shop_temp_order_items (order_id, p_id, items) VALUES (".$id_order.", ".$p_id.", ".$items.")";
		$lastId = ConnectDB::subExec($sql);
		return $lastId;
	}

	public function deleteTempItemAjax($id_order, $p_id) {

		$sql = "DELETE FROM shop_temp_order_items WHERE order_id = ".$id_order." AND p_id = ".$p_id;
		ConnectDB::subExec($sql);
		return true;
	}

	public function getOrderList($filtr = null, $limit = null, $order = null, $status = '', $date_start = '', $date_stop = '') {

		$sql = "SELECT * FROM shop_order LEFT JOIN shop_devilery USING(devilery_id)
										 JOIN shop_order_status USING(status_id)
										 LEFT JOIN shop_country ON(o_country = shop_country.id_country)
										 LEFT JOIN shop_users ON(shop_users.user_id = user_panel)
										 ";


		$sql .= $this->getOrderFiltr($filtr);
		$sql .= $this->getOrderLimit($limit);
		$sql .= $this->getOrderOrder($order);

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	public function getOrderCount($filtr = null) {

		$sql = "SELECT count(*) FROM shop_order 
				JOIN shop_order_status USING(status_id)
				WHERE 1";

		//$sql .= $this->getOrderFiltr($filtr);

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getOrderFiltr($filtr) {


		if($filtr['date_start'])
			$date_start = $filtr['date_start'];
		else
			$date_start = date('Y-m-d', strtotime('-3 day', strtotime(date("Y-m-d"))));

		$date_start .= ' 23:59:59';
		$sgl .= " WHERE o_datetime >= '".$date_start."'";


		if($filtr['date_stop'])
			$date_stop = $filtr['date_stop'];
		else
			$date_stop = date("Y-m-d");

		$date_stop .= ' 23:59:59';
		$sgl .= " AND o_datetime <= '".$date_stop."'";


		if($filtr['status'] != 0)
			$sql .= "AND status_id = ".$filtr['status'];

		return $sql;
	}

	public function getOrderLimit($limit) {

		$sql = "";
		return $sql;
	}

	public function getOrderORder($order) {

		$sql = "ORDER BY o_datetime DESC";
		return $sql;
	}

	public function getOrder($id_order) {

		$sql = "SELECT * FROM shop_order JOIN shop_order_items USING(o_id)
										 JOIN shop_order_status USING(status_id)
										 JOIN shop_product USING(p_id)
										 LEFT JOIN shop_country ON(o_country = id_country)
										 LEFT JOIN shop_devilery USING(devilery_id)
										 LEFT JOIN shop_categories_products USING(p_id)
										 LEFT JOIN shop_categories USING(cat_id)
										 LEFT JOIN shop_product_media on (shop_product_media.p_id = shop_product.p_id)
										 WHERE shop_order.o_id = ".$id_order." GROUP BY shop_order_items.p_id";
		

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			foreach($aResult as &$row) {
				$row['link'] = "p-".Misc::utworzSlug($row['p_name']).",".$row['p_id'];
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function saveOrderStatus($o_id, $status_id, $notify = 0, $nr_send = '') {
		$add = '';
		if($nr_send != '') {
			$add = ', o_supply_number = "'.$nr_send.'"';
		}
		$sql = "UPDATE shop_order SET status_id = ".$status_id.$add." WHERE o_id = ".$o_id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		if($notify) {
			$obMail = new Mail();
			if($status_id == 3) {
				$obMail -> setSubject("Potwierdzenie realizacji zamówienia ZAM_".$o_id . date("Y")." - Zambi.pl");
				$obMail -> generateMailTemplate('realizacja', $o_id);
			}
			elseif($status_id == 2) {
				$obMail -> setSubject("Potwierdzenie przygotowania wysyłki zamówienia ZAM_".$o_id . date("Y")." - Zambi.pl");
				$obMail -> generateMailTemplate('przygotowane', $o_id);
			}
			elseif($status_id == 4) {
				$obMail -> setSubject("Potwierdzenie wysyłki zamówienia ZAM_".$o_id . date("Y")." - Zambi.pl");
				$obMail -> generateMailTemplate('wyslano', $o_id);
			}
			elseif($status_id == 5) {
				$obMail -> setSubject("Zamówienie ZAM_".$o_id . date("Y")." zostało anulowane - Zambi.pl");
				$obMail -> generateMailTemplate('anulowano', $o_id);
			}
			$obMail -> send();
		}
		return $aResult;
	}

	public function saveOrderReceipt($o_id, $pay_nr) {

		$sql = "UPDATE shop_order SET o_receipt = '".$pay_nr."' WHERE o_id = ".$o_id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}

		return $aResult;
	}

	public function delOrder($id_order) {

		$sql = "DELETE FROM shop_order_items WHERE o_id = ".$id_order;
		$sql2 = "DELETE FROM shop_order WHERE o_id = ".$id_order;

		ConnectDB::subExec($sql);
		ConnectDB::subExec($sql2);
		return true;
	}

	public function getStatusList() {
		$sql = "SELECT * FROM `shop_order_status`";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function generateInvoice($id) {

		include_once("libraries/tcpdf/config/lang/eng.php");
		include_once("libraries/tcpdf/tcpdf.php");

		$aOrder = $this -> getOrder($id);

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('TVS');
		$pdf->SetTitle('Faktura PRO-FORMA '.$aOrder[0]['o_id'].'/'.date("Y"));
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		//$pdf->SetHeaderData('tvs-logo.png', PDF_HEADER_LOGO_WIDTH, 'Faktura PRO-FORMA '.$aOrder[0]['o_id'].'/'.date("Y"));

		// set header and footer fonts
		$pdf->setHeaderFont(Array('freemono', '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
		$html = '<div><div><table>
<tbody><tr><td align="center">
<table cellpadding="0" cellspacing="0">
<tbody><tr><td>FAKTURA PRO-FORMA nr PRO/'.$aOrder[0]['o_id'].'/'.date("Y").'</td></tr>
<tr><td>ORYGINAŁ</td></tr>
</tbody></table>
<br><br><table width="100%" cellpadding="0" cellspacing="0">
<tbody><tr>
<td style="width:120px">Sprzedawca:</td>
<td style="text-align:left;vertical-align:top;padding-right:50px; margin-right: 50px; width: 200px;">TVS Sp. z o.o.<br />Plac Grunwaldzki 12<br>40-126 Katowice<br>
                                    NIP 6342617215<br>
</td>
<td style="width:120px">Nabywca:</td>
<td style="text-align:left;vertical-align:top;padding-right:50px; width: 200px;">'.$aOrder[0]['o_customer_name'].'<br>'.$aOrder[0]['o_adress1'].'<br/>'.$aOrder[0]['o_zip'].' '.$aOrder[0]['o_city'].'<br>
                                    </td>
</tr>
</tbody></table>
</td></tr>
<tr><td align="left">
<br><table width="100%" cellpadding="0" cellspacing="0">
<tbody><tr>
<td style="padding-right:0px">
<b>Data wystawienia: </b>'.substr($aOrder[0]['o_datetime'],0,10).'</td>

<td style="padding-right:0px">
<b>Forma płatności: </b>przy odbiorze (za pobraniem)</td>
</tr>
</tbody></table>

<br>
</td></tr>
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" style="width: 95%">
<tbody><tr>
<td style="margin-bottom: 10px; text-align: center">Lp.</td>
<td style="width: 110px;margin-bottom: 10px;">Nazwa towaru/usługi</td>
<td style="margin-bottom: 10px;text-align:center">Ilość</td>
<td style="margin-bottom: 10px;text-align:center">J.m.</td>
<td style="margin-bottom: 10px;text-align:center">Cena<br>netto</td>
<td style="margin-bottom: 10px;text-align:center">Wartość<br>netto</td>

<td style="margin-bottom: 10px;text-align:center">VAT</td>
<td style="margin-bottom: 10px;text-align:center">Wartość<br>VAT</td>
<td style="margin-bottom: 10px;text-align:center">Wartość<br>brutto</td>
</tr>';
$i = 1; $vat = 0; $netto = 0;
foreach($aOrder as $row) {
	$html .= '<tr>
	<td style="text-align:center">'.$i.'</td>
	<td>'.$row['i_name'].'</td>
	<td style="text-align:center">'.$row['i_pieces'].'</td>
	<td style="text-align:center">szt.</td>

	<td style="text-align:center">'.Misc::showPrice($row['p_price']).'</td>
	<td style="text-align:center">'.Misc::showPrice($row['p_price']).'</td>
	<td style="text-align:center"> 22%</td>
	<td style="text-align:center">'.Misc::showPrice($row['p_price_gross']-$row['p_price']).'</td>
	<td style="text-align:center">'.Misc::showPrice($row['i_pieces']*$row['p_price_gross']).'</td>
	</tr>';
	$i++;
	$vat += $row['p_price_gross']-$row['p_price'];
	$netto += $row['p_price'];
}

$t_netto = ($aOrder[0]['o_fee']*(22/100));
$netto += $t_netto;
$vat += $aOrder[0]['o_fee']-$t_netto;

$html .= '
<tr>
	<td style="text-align:center">'.$i.'</td>
	<td>Transport - '.$aOrder[0]['transport_name'].'</td>
	<td style="text-align:center">1</td>
	<td style="text-align:center">szt.</td>

	<td style="text-align:center">'.Misc::showPrice($t_netto).'</td>
	<td style="text-align:center">'.Misc::showPrice($t_netto).'</td>
	<td style="text-align:center"> 22%</td>
	<td style="text-align:center">'.Misc::showPrice($aOrder[0]['o_fee']-$t_netto).'</td>
	<td style="text-align:center">'.Misc::showPrice($aOrder[0]['o_fee']).'</td>
	</tr>
<tr><td colspan="9"><br></td></tr>
<tr>
<td colspan="4"></td>
<td style="text-align:right;border-top:1px solid rgb(0, 0, 0)"><b>razem:</b></td>

<td style="text-align:right;border-top:1px solid rgb(0, 0, 0)"><b>'.Misc::showPrice($netto).'</b></td>
<td style="text-align:right;border-top:1px solid rgb(0, 0, 0)">22%</td>
<td style="text-align:right;border-top:1px solid rgb(0, 0, 0)"><b>'.Misc::showPrice($vat).'</b></td>
<td style="text-align:right;border-top:1px solid rgb(0, 0, 0)"><b>'.Misc::showPrice($aOrder[0]['o_sum']).'</b></td>
</tr>
</tbody></table>
<br>
</td></tr>
<tr><td align="left"><table cellpadding="0" cellspacing="0">
<tbody><tr><td style="padding-right:0px"><b>Płatności:</b></td></tr>
<tr><td style="padding-right:0px">
                                        Do zapłaty:
                                    <b>'.Misc::showPrice($aOrder[0]["o_sum"]).'</b> zł.
                            </td></tr>

</tbody></table></td></tr>
<tr><td>
                            <br /><br /><br />Faktura vat/rachunek zostaną dostarczone po zaksięgowaniu wpłaty na naszym koncie.
                        </td></tr>
</tbody></table></div></div>';
//echo $html;
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$name = $aOrder[0]['o_id'].'_pro-forma_'.substr($aOrder[0]['o_datetime'],0,10).'.pdf';
		$pdf->Output(MyConfig::getValue("serverPatch").'avoids/'.$name, 'FD');
	}

	public function generatePostTicket($aOrder) {

		include('libraries/tcpdf/config/lang/pol.php');
		include('libraries/tcpdf/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'mm', '', true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Sklep TVS');
		$pdf->SetTitle('Tytul');
		$pdf->SetSubject('Podtytul');

		// set header and footer fonts
		$pdf->setHeaderFont('');
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(5,29,5);
		$pdf->SetHeaderMargin(34);
		$pdf->SetFooterMargin(0);
		$pdf->setPageFormat(array('101.6','101.6'));

		//set auto page breaks
		$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 19);

		// add a page
		$pdf->AddPage();

		// create some HTML content
		$html = '<span style="font-size: 30px;">zam. 000'.$aOrder[0]['o_id'].'</span><br />'.$aOrder[0]['o_customer_name'].'<br />'.$aOrder[0]['o_adress1'].'<br />'.$aOrder[0]['o_zip'].' '.$aOrder[0]['o_city'].'<br />';

		// output the HTML content
		$pdf->writeHTML($html, true, 0, true, 0);

		// reset pointer to the last page
		$pdf->lastPage();

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output(MyConfig::getValue("serverPatch").'admin/naklejki/postticket_'.$aOrder[0]['o_id'].'.pdf', 'I');
		header("Content-type: application/pdf");
		header("Content-Length: $mylen");
		header("Content-Disposition: attachment; filename=".$wwwPatch."admin/naklejki/postticket_".$aOrder[0]['o_id'].".pdf");
		return true;
	}

	public function editOrderAjax($id, $param, $value) {
		
		$sql = "UPDATE shop_order SET ".$param." = '".$value."' WHERE o_id = ".$id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return $aResult;
	}

	public function editOrderItemAjax($id, $param, $value) {

		$sql = "UPDATE shop_order_items SET ".$param." = '".$value."' WHERE i_id = ".$id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return $aResult;
	}

	public function calculateOrderItem($id) {

		$sql = "UPDATE shop_order_items SET i_sum = (i_pieces*i_price) WHERE i_id = ".$id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			$aResult = ConnectDB::subQuery("SELECT i_sum FROM shop_order_items WHERE i_id = ".$id);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return Misc::showPrice($aResult[0]['i_sum']);
	}

	public function calculateOrder($id, $fee = 1) {

		$aResult = $this -> getOrder($id);
		$sum = 0;
		$items = 0;
		foreach($aResult as $row) {
			$sum += $row['i_price']*$row['i_pieces'];
			$items++;
		}

		$sql = "UPDATE shop_order SET o_items = ".$items.", o_sum = (".$sum."+o_fee) WHERE o_id = ".$id;
		ConnectDB::subExec($sql);
		if($fee)
			$aResult2 = ConnectDB::subQuery("SELECT (o_sum-o_fee) as o_sum FROM shop_order WHERE o_id = ".$id);
		else
			$aResult2 = ConnectDB::subQuery("SELECT o_sum FROM shop_order WHERE o_id = ".$id);

		return Misc::showPrice($aResult2[0]['o_sum']);
	}

	public function calculateOrderFee($id) {

		$aResult = $this -> getOrder($id);
		$sql = "SELECT devilery_price FROM shop_devilery WHERE devilery_id = ".$aResult[0]['devilery_id'];

		try {
			$aResult2 = array();
			$aResult2 = ConnectDB::subQuery($sql);
			$this -> editOrderAjax($id,'o_fee' ,$aResult2[0]['devilery_price']);
			$this -> editOrderAjax($id,'o_sum' ,($aResult[0]['o_sum']-$aResult[0]['o_fee'])+$aResult2[0]['devilery_price']);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return Misc::showPrice($aResult2[0]['devilery_price']);
	}

	public function getDevileryAjax() {

		$sql = "SELECT * FROM shop_devilery LEFT JOIN shop_country USING(id_country) ORDER BY devilery_order, country";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			$aResult2 = array();
			foreach($aResult as $row) {
				$aResult2[$row['devilery_id']] = $row['devilery_name'].' ('.$row['country'].')';
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return $aResult2;
	}

	public function getDevileryName($id) {
		$sql = "SELECT * FROM shop_devilery WHERE devilery_id = ".$id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
		}

		return $aResult;
	}

	public function deleteOrderItems($items) {

	}

	public function addOrderItems($id_order, $id_item, $items) {

	    $aProduct = $this ->getProduct($id_item);
	    if($aProduct[0]['p_promo_price'] != 0)
		$cena = $aProduct[0]['p_promo_price'];
	    else $cena = $aProduct[0]['p_price_gross'];

	    $pdo = new ConnectDB();
	    $pdo -> exec("SET names utf8 ");
	    $sql = "INSERT INTO shop_order_items (o_id, i_name, p_id, i_pieces, i_price, i_sum) VALUES (".$id_order.", '".$aProduct[0]['p_name']."', ".$id_item.", ".$items.", ".$cena.", ".  floatval($cena*$items).")";
	    $pdo -> exec($sql);

	    $aProduct[0]['i_pieces'] = $items;
	    $aProduct[0]['id_order'] = $id_order;
	    $aProduct[0]['i_price'] = number_format(floatval($cena), 2);
	    $aProduct[0]['i_id'] = $pdo->lastInsertId();
	    $aProduct[0]['i_sum'] = number_format(floatval($cena*$items), 2);

	    echo json_encode($aProduct);
	    return true;

	}

	public function getProduct($id) {

		$sql = "SELECT * FROM shop_product JOIN shop_authors USING(author_id)
			JOIN shop_categories_products USING(p_id) JOIN shop_categories USING(cat_id)
			LEFT JOIN shop_product_media on (shop_product_media.p_id = shop_product.p_id AND m_jpg = 1)
			WHERE shop_product.p_id = ".$id;

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function generateSiodemkaTicket($aOrder) {

	    $aDane = array();
	    $aImie = explode(" ", $aOrder[0]['o_customer_name']);
	    $aDane['imie']	= $aImie[0];
	    $aDane['nazwisko']	= $aImie[1];

	    $aAdress = explode(" ", trim($aOrder[0]['o_adress1']));
	    $cnt = count($aAdress);
	    $nr = $aAdress[$cnt-1];
	    $aAdress[$cnt-1] = '';
	    if(strstr($nr, '/')) {
		$aDane['nr_mieszkanie'] = substr(strstr($nr, '/'), 1);
		$aDane['nr_dom'] = strstr($nr, '/', true);
	    } else {
		$aDane['nr_dom'] = $nr;
		$aDane['nr_mieszkanie'] = '';
	    }
	    $aDane['ulica'] = implode(" ", $aAdress);
	    $aDane['ubezpieczenie'] = round(floatval(($aOrder[0]['o_sum']*0.1)+$aOrder[0]['o_sum']));
	    $aDane['miasto'] = $aOrder[0]['o_city'];
	    $aDane['telefon'] = $aOrder[0]['o_phone'];
	    $aDane['kod_pocztowy'] = $aOrder[0]['o_zip'];
	    $aDane['opis'] = "Płyty CD";
	    $aDane['o_id'] = $aOrder[0]['o_id'];

	    if(!$aOrder[0]['bank'] && !$aOrder[0]['dotpay']) {
		$aDane['pobranie'] = $aOrder[0]['o_sum'];
	    } else $aDane['pobranie'] = '';

	    return $aDane;
	}

	public function sendSiodemkaTicket() {

	    if($_POST['nr_mieszkanie'] == '')
		$mieszkanie = '';
	    else
		$mieszkanie = $_POST['nr_mieszkanie'];
	    //print_r($aOrder);
	    $tablica_7 = array();
	    $tablica_7 = array('listNadanieElement' =>
				array('przesylka' =>
				    array('rodzajPrzesylki' => 'K',
					  'placi' => 1,
					  'formaPlatnosci' => 'P',
					  'nadawca' =>
						array(
						    'numer' => '8244134',
						    'telKontakt' => '326089934',
						    'emailKontakt' => ''
						),
					   'odbiorca' =>
						array('miasto' =>	$_POST['miasto'],
						      'ulica' =>	$_POST['ulica'],
						      'telKontakt' =>	$_POST['telefon'],
						      'nrDom' =>	$_POST['nr_dom'],
						      'kodKraju' =>	'PL',
						      'nrLokal' =>	$mieszkanie,
						      'imie' =>		$_POST['imie'],
						      'nazwisko' =>	$_POST['nazwisko'],
						      'kod' =>		$_POST['kod_pocztowy'],
						      'czyFirma' => 0,
						      'numer' => ''
						),
					    'uslugi' =>
						array(
						    'ubezpieczenie' =>
							array('kwotaUbezpieczenia' => $_POST['ubezpieczenie'], 'opisZawartosci' => $_POST['opis'])
						),
					    'paczki' =>
						array('paczka' =>
						    array('typ' => $_POST['typ'],
							  'waga' => $_POST['waga'],
							  'ksztalt' => 0)),
					    'potwierdzenieNadania' =>
						array('dataNadania' => date("Y-m-d H:i", strtotime('+7 hours')),
						      'numerKuriera' => '345',
						      'podpisNadawcy' => 'Jurdziński'
						),
					    'nrExt' => $_POST['o_id']

				),
				'klucz' => 'AA0F0B04AC1A1D3CCDF31F1FBBC885FB'
			  )
		    );

	     if($_POST['pobranie'] != '') {
		$tablica_7['listNadanieElement']['przesylka']['uslugi']['pobranie'] = array('kwotaPobrania' => $_POST['pobranie'], 'formaPobrania' => 'B', 'nrKonta' => '29105012141000002350729402');
	    }
	
	    try {
		    $client = new SoapClient('http://webmobile7.siodemka.com/mm7ws/SiodemkaServiceSoapHttpPort?WSDL');
		    $result = $client -> __call("listNadanie", $tablica_7);
		    $nrPrzesylki = $result -> result -> nrPrzesylki;

		    $sql = "UPDATE shop_order SET o_supply_number  = ".$nrPrzesylki." WHERE o_id = ".$_POST['o_id']." LIMIT 1";
		    try {
			    $aResult = array();
			    $aResult = ConnectDB::subExec($sql);
		    }
		    catch(PDOException $e) {
			    Log::SLog($e->getTraceAsString());
			    header("Location: ".MyConfig::getValue("wwwPatch"));
		    }

		    //wydruk najlejki
		    $result2 = $client -> __call("wydrukEtykietaPdf", array('wydrukEtykietaPdfElement' => array('klucz' => "AA0F0B04AC1A1D3CCDF31F1FBBC885FB", 'numery' => $nrPrzesylki, 'separator' => '')));

		    header("Content-type: application/pdf");
		    echo $result2 -> result;

	    } catch (Exception $e) {
		    echo "<h2>Exception Error!</h2>";
		    echo $e->getMessage();
		    echo $e->getTraceAsString();
	    }
	}
	
}
?>
