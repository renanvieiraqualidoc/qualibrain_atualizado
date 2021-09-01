<?xml version="1.0"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
  	<channel>
    		<title>Qualidoc - <?=date('d-m-Y G:i:s');?></title>
    		<description>Igual a nenhuma farmácia</description>
    		<link>https://www.qualidoc.com.br/</link>
        <?php foreach($items as $row):?>
    		<item>
      			<g:id><?=$row->sku;?></g:id>
      			<title><?=$row->title;?></title>
      			<description><?=$row->description;?></description>
      			<link><?=$row->link;?></link>
      			<g:image_link><?=$row->image_link;?></g:image_link>
      			<g:product_type><?=$row->product_type;?></g:product_type>
      			<g:google_product_category><?=$row->google_product_category;?></g:google_product_category>
      			<g:brand><?=$row->brand;?></g:brand>
      			<g:gtin><?=$row->gtin;?></g:gtin>
      			<g:mpn><?=$row->sku;?></g:mpn>
      			<g:price><?=$row->price." BRL";?></g:price>
      			<g:custom_label_0>0</g:custom_label_0>
            <g:installment>
                <g:months>1</g:months>
            		<g:amount><?=$row->price." BRL";?></g:amount>
            </g:installment>
      			<g:condition>new</g:condition>
      			<g:availability>in stock</g:availability>
    		</item>
        <?php endforeach;?>
    </channel>
</rss>
