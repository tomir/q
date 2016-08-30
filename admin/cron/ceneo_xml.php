<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">
<group name="other">
<?php foreach($aProductList as $item) {
	if(!empty($item)) { ?>
	<o id="<?php echo $item['p_id']; ?>" url="http://www.zambi.pl/<?php echo $item['url']; ?>" price="<?php echo $item['p_price_gross']; ?>" avail="<?php if($item['p_magazine'] > 0) echo '1'; else echo '99'; ?>"
set="0" basket="1" stock="<?php echo $item['p_magazine']; ?>">
		<name>
			<![CDATA[<?php echo $item['p_name']; ?>]]>
		</name>   
		<cat><![CDATA[<?php echo $item['cats']; ?>]]></cat>
		<imgs>
			<main url="http://www.zambi.pl/temp/product_images/800x800/<?php echo $item['m_i']; ?>.jpg"/>
		</imgs>
		<desc>
			<![CDATA[]]>
		</desc>
		<attrs>
			<a name="Producent">
				<![CDATA[<?php echo $item['producent_name']; ?>]]>
			</a>
			<a name="Kod_producenta">
				<![CDATA[]]>
			</a>
			<a name="EAN">
				<![CDATA[<?php echo $item['import_ean']; ?>]]>
			</a>        
		</attrs>
	</o>
	<?php } 
} ?>
</group>
</offers>