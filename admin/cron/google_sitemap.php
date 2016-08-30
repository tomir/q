<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach($urls as $key=>$item) {
	if(!empty($item)) { ?>
	<url>
		<loc><![CDATA[http://www.<?php echo 'zambi.pl/'.$item; ?>]]></loc>
		<changefreq>daily</changefreq>
	</url>
	<?php } 
} ?>
</urlset>