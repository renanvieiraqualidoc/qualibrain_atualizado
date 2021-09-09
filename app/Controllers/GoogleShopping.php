<?php

namespace App\Controllers;
use App\Models\ProductsModel;

class GoogleShopping extends BaseController
{
	public function index($data = []) {
			$data['categories'] = $this->dynamicMenu();
			echo view('google_shopping', $data);
	}

	// Função que monta o XML de produtos do Google Shopping
	public function xml() {
			$model = new ProductsModel();
			$items = $model->getProductsGoogleShopping();
			$doc = new \DOMDocument('1.0', 'UTF-8');
			$xmlRoot = $doc->createElement("rss");
			$xmlRoot = $doc->appendChild($xmlRoot);
			$xmlRoot->setAttribute('version', '2.0');
			$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', "http://base.google.com/ns/1.0");
			$channelNode = $xmlRoot->appendChild($doc->createElement('channel'));
			$channelNode->appendChild($doc->createElement('title', 'Qualidoc -'.date('d-m-Y G:i:s')));
			$channelNode->appendChild($doc->createElement('description', 'Igual a nenhuma farmácia'));
			$channelNode->appendChild($doc->createElement('link', 'https://www.qualidoc.com.br/'));
			foreach ($items as $product) {
					$itemNode = $channelNode->appendChild($doc->createElement('item'));
					$itemNode->appendChild($doc->createElement('g:id'))->appendChild($doc->createTextNode($product->sku));
					$itemNode->appendChild($doc->createElement('title'))->appendChild($doc->createTextNode($product->title));
					$itemNode->appendChild($doc->createElement('description'))->appendChild($doc->createTextNode($product->description));
					$itemNode->appendChild($doc->createElement('link'))->appendChild($doc->createTextNode($product->link));
					$itemNode->appendChild($doc->createElement('g:image_link'))->appendChild($doc->createTextNode($product->image_link));
					$itemNode->appendChild($doc->createElement('g:product_type'))->appendChild($doc->createTextNode($product->product_type));
					$itemNode->appendChild($doc->createElement('g:google_product_category'))->appendChild($doc->createTextNode($product->google_product_category));
					$itemNode->appendChild($doc->createElement('g:brand'))->appendChild($doc->createTextNode($product->brand));
					$itemNode->appendChild($doc->createElement('g:gtin'))->appendChild($doc->createTextNode($product->gtin == 11 ? "00".$product->gtin : $product->gtin));
					$itemNode->appendChild($doc->createElement('g:mpn'))->appendChild($doc->createTextNode($product->sku));
					$itemNode->appendChild($doc->createElement('g:price'))->appendChild($doc->createTextNode($product->price." BRL"));
					$itemNode->appendChild($doc->createElement('g:custom_label_0'))->appendChild($doc->createTextNode($product->cashback > 0 ? '1' : '0'));
					$months = 1;
					if ($product->price >= 100) $months = 2;
					if ($product->price >= 150) $months = 3;
					$itemNode->appendChild($doc->createElement('g:installment'))->appendChild($doc->createElement('g:months'))->appendChild($doc->createTextNode($months));
					$itemNode->appendChild($doc->createElement('g:installment'))->appendChild($doc->createElement('g:amount'))->appendChild($doc->createTextNode(str_replace(",", ".", number_to_amount($product->price/$months, 2, 'pt_BR'))." BRL"));
					$itemNode->appendChild($doc->createElement('g:condition'))->appendChild($doc->createTextNode("new"));
					$itemNode->appendChild($doc->createElement('g:availability'))->appendChild($doc->createTextNode("in stock"));
			}
			$doc->formatOutput = true;
			$fileName = "xml_google_shopping.xml";
			$doc->save("xml/".$fileName);
			return $this->response->download("xml/".$fileName, null);
	}
}
