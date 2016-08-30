<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE nokaut SYSTEM "http://www.nokaut.pl/integracja/nokaut.dtd">
<nokaut generator="MyOwnShop" ver="1.0">
	<offers>
	<?php foreach($aProductList as $item) {
		if(!empty($item) && $item['import_ean'] != '') { ?>
		<offer>
			<id><?php echo $item['p_id']; ?></id>
			<name><![CDATA[<?php echo $item['p_name']; ?>]]></name>
			<description><![CDATA[<?php echo strip_tags($item['p_description']); ?>]]></description>
			<url><![CDATA[http://www.zambi.pl/<?php echo $item['url']; ?>]]></url>
			<image><![CDATA[http://www.zambi.pl/temp/product_images/800x800/<?php echo $item['m_i']; ?>.jpg]]></image>
			<price><?php echo $item['p_price_gross']; ?></price>
			<category><![CDATA[<?php echo $item['cats']; ?>]]></category>
			<producer><![CDATA[<?php echo $item['producent_name']; ?>]]></producer>
			<property name="EAN"><?php echo $item['import_ean']; ?></property>
			<promo><![CDATA[Darmowa dostawa dla zamówień powyżej 399zł]]></promo>
			<instock><?php echo $item['p_magazine']; ?></instock>
			<availability><?php if($item['p_magazine'] > 0) echo '0'; else echo '4'; ?></availability>
			<shipping>9.00</shipping>
		</offer>
		<?php } 
	} ?>
	</offers>
</nokaut>