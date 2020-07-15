<?php
if($_POST['download'] == '1'){//check command
//header('Content-Type: text/html; charset=utf-8');
include_once 'config.php'; // get config variables $db_prefix, $db_user, $db_pass, $db_host, $db_name, $urlSite, $nameSite, $nameCompany
spl_autoload_register(function ($class_name) {
   include 'class/'.$class_name . '.php';
});
$db                 = new Db($db_prefix, $db_user, $db_pass, $db_host, $db_name);
$fileName           = 'export.csv';
$fileCsv            = fopen($fileName,"w");
$res                = array('id',
    'code',
    'quantity',
    'price',
    'image',
    'image2',
    'image3',
    'image4',
    'nameRu',
    'aliasRu',
    'shortDescRu',
    'DescRu',
    'TitleRu',
    'MetaDescRu',
    'nameUa',
    'aliasUa',
    'shortDescUa',
    'DescUa',
    'TitleUa',
    'Length',
    'Width',
    'Width2',
    'Height'
);
fputcsv($fileCsv, $res, ';');
$csvArray           = array();
$connect            = $db->connect();
$pImgCollection     = array();
$separatorImg       = '|';
//PRODUCT
  $productInfo    = $db->load_data( $connect , 'jshopping_products');//data
  foreach($productInfo as $product){
      $i            = 0;
      $productImage = $db->load_data( $connect , 'jshopping_products_images', 'product_id',$product['product_id']);//data img by id product
      foreach ($productImage as $img) {
          if ($i < 4) { //how much image load
              $pImgCollection[$i] = 'http://www.site.net/img_products/'.$img['image_name'];
          }
          $i++;
      }
      $productLength = $db->load_data( $connect , 'jshopping_products_extra_field_values', 'id', $product['extra_field_6']);//get length extra fields
      $width = (strpos($product['extra_field_5'],',') === false) ? array($product['extra_field_5'], '') : explode(',',$product['extra_field_5']);
      $productWidth = $db->load_data( $connect , 'jshopping_products_extra_field_values', 'id', $width[0]);//get width 1 extra fields
      $productWidth2 = $db->load_data( $connect , 'jshopping_products_extra_field_values', 'id', $width[1]);//get width 2 extra fields
      $productHeight = $db->load_data( $connect , 'jshopping_products_extra_field_values', 'id', $product['extra_field_7']);//get width 2 extra fields

      $csvArray[] = array($product['product_id'],
          $product['manufacturer_code'],
          $product['product_quantity'],
          $product['product_price'],
          ($pImgCollection[0] == 'http://www.site.net/img_products/'.$product['image'])  ? $pImgCollection[0] : 'http://www.site.net/img_products/'.$product['image'] ,
          $pImgCollection[1],
          $pImgCollection[2],
          $pImgCollection[3],
          $product['name_ru-RU'],
          $product['alias_ru-RU'],
          $product['short_description_ru-RU'],
          $product['description_ru-RU'],
          $product['meta_title_ru-RU'],
          $product['meta_description_ru-RU'],
          $product['name_uk-UA'],
          $product['alias_uk-UA'],
          $product['short_description_uk-UA'],
          $product['description_uk-UA'],
          $product['meta_title_uk-UA'],
          $productLength[0]['name_uk-UA'],
          $productWidth[0]['name_uk-UA'],
          $productWidth2[0]['name_uk-UA'],
          $productHeight[0]['name_uk-UA']
      );
  }

    foreach($csvArray as $field) {
        fputcsv($fileCsv,$field, ';');
    }

    fclose($fileName); // закрываем файл
    header('Content-type: application/csv');
    header("Content-Disposition: inline; filename=".$fileName);
    readfile($fileName);
    unlink($fileName);
}else{
    echo 'ERROR!';
}
