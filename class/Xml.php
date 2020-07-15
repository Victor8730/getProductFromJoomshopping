<?php
/**
 * Array to xml
 * Append xml
 * Class fro working with xml file and SimpleXMLElement
 */
class Xml
{

	public $path;
	
	public function __construct($path) {
		$this->path	= $path;
	}

	public function getXmlArray() {
		if (file_exists($this->path)) {
			$array = array();
			$xml = simplexml_load_file($this->path) or die('Error: Cannot create object');
			foreach ($xml->nomenklatura as $item) {
				$array[] = (string)$item->article;
			}
			return $array;
		}else{
			return false;
		}
	}

    public function arrayToXml( $data, $xml_data, $name= 'category') {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if($name=='category') {
                    $subnode = $xml_data->addChild($name, htmlspecialchars($value['name']));
                    $subnode->addAttribute('id', $key);
                    if(isset($value['parent'])) $subnode->addAttribute('parentId', $value['parent']);
                }elseif($name=='offer'){
                    $subnode = $xml_data->addChild($name);
                    $subnode->addAttribute('id', $key);
                    if(isset($value['available'])) $subnode->addAttribute('available', $value['available']);
                    if(isset($value['url'])) $subnode->addChild('url', $value['url']);
                    if(isset($value['price'])) $subnode->addChild('price', $value['price']);
                    if(isset($value['currencyId'])) $subnode->addChild('currencyId', $value['currencyId']);
                    if(isset($value['categoryId'])) $subnode->addChild('categoryId', $value['categoryId']);
                    if(isset($value['picture'])) {
                        if(is_array($value['picture'])){
                            foreach($value['picture'] as $pict){
                                    $subnode->addChild('picture', $pict);
                            }
                        }else{
                            $subnode->addChild('picture', $value['picture']);
                        }
                    }
                    if(isset($value['vendor'])) $subnode->addChild('vendor', $value['vendor']);
                    if(isset($value['stock_quantity'])) $subnode->addChild('stock_quantity', (int)$value['stock_quantity']);
                    if(isset($value['name'])) $subnode->addChild('name', $value['name']);
                    if(isset($value['product_desc'])) $subnode->addChild('description', '<![CDATA['.$value['product_desc'].']]>');
                    if(isset($value['product_sku'])) {
                        $subsubnode = $subnode->addChild('param', (int)$value['product_sku']);
                        $subsubnode->addAttribute('name', 'Артикул');
                    }
                }elseif($name=='currency'){
                    $subnode = $xml_data->addChild($name);
                    $subnode->addAttribute('id', $key);
                    if(isset($value['name'])) $subnode->addAttribute('rate', $value['name']);
                }else{
                    if(isset($value['name'])) $xml_data->addChild('name',$value['name'] );
                    if(isset($value['company'])) $xml_data->addChild('company',$value['company'] );
                    if(isset($value['url'])) $xml_data->addChild('url',$value['url'] );
                }
            } else {
                $subnode =$xml_data->addChild($name,htmlspecialchars($value));
                $subnode->addAttribute('id', $key);
            }
        }
        return $xml_data;
    }

    public function appendXml(SimpleXMLElement $parent, $xml, $before = false)
    {
        $xml = (string)$xml;
        // check if there is something to add
        if ($nodata = !strlen($xml) or $parent[0] == NULL) {
            return $nodata;
        }
        // add the XML
        $node     = dom_import_simplexml($parent);
        $fragment = $node->ownerDocument->createDocumentFragment();
        $fragment->appendXML($xml);
        if ($before) {
            return (bool)$node->parentNode->insertBefore($fragment, $node);
        }
        return (bool)$node->appendChild($fragment);
    }

    public function appendXmlToXml(SimpleXMLElement $parent, SimpleXMLElement $child, $before = false)
    {
        // check if there is something to add
        if ($child[0] == NULL) {
            return true;
        }
        // if it is a list of SimpleXMLElements default to the first one
        $child = $child[0];
        // insert attribute
        if ($child->xpath('.') != array($child)) {
            $parent[$child->getName()] = (string)$child;
            return true;
        }
        $xml = $child->asXML();
        // remove the XML declaration on document elements
        if ($child->xpath('/*') == array($child)) {
            $pos = strpos($xml, "\n");
            $xml = substr($xml, $pos + 1);
        }
        return $this->appendXml($parent, $xml, $before);
    }
	
}