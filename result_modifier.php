<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */

/** @var array $arResult */

use Bitrix\Main;

$defaultParams = array(
    'TEMPLATE_THEME' => 'blue'
);
$arParams = array_merge($defaultParams, $arParams);
unset($defaultParams);

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME']) {
    $arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
    if ('site' == $arParams['TEMPLATE_THEME']) {
        $templateId = (string)Main\Config\Option::get('main', 'wizard_template_id', 'eshop_bootstrap', SITE_ID);
        $templateId = (preg_match("/^eshop_adapt/", $templateId)) ? 'eshop_adapt' : $templateId;
        $arParams['TEMPLATE_THEME'] = (string)Main\Config\Option::get('main', 'wizard_' . $templateId . '_theme_id', 'blue', SITE_ID);
    }
    if ('' != $arParams['TEMPLATE_THEME']) {
        if (!is_file($_SERVER['DOCUMENT_ROOT'] . $this->GetFolder() . '/themes/' . $arParams['TEMPLATE_THEME'] . '/style.css'))
            $arParams['TEMPLATE_THEME'] = '';
    }
}
if ('' == $arParams['TEMPLATE_THEME'])
    $arParams['TEMPLATE_THEME'] = 'blue';


foreach ($arResult['GRID']['ROWS'] as $item) {
    $arImportCart[$item['PRODUCT_ID']]['PRODUCT_ID'] = $item['PRODUCT_ID'];
    $arImportCart[$item['PRODUCT_ID']]['QUANTITY'] = $item['QUANTITY'];
}


if (stripos($APPLICATION->GetCurPage(), 'basket/import/')) {
    $link = $APPLICATION->GetCurPage();
    $strItems = '';
    $name_link = stristr(str_replace('/basket/import/', '', $link), '/', true);

    $arSelect = Array("ID", "NAME", "PREVIEW_TEXT");
    $arFilter = Array("IBLOCK_ID" => 26, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $name_link);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $strItems = $arFields['PREVIEW_TEXT'];
    }
    $strItems = str_replace(['{', '}'], '', $strItems);
    $arItmes = explode(',', $strItems);
    foreach ($arItmes as $strIdProd_quantity) {
        $arIdProd_quantity = explode(':', $strIdProd_quantity);
        $arArItems[] = $arIdProd_quantity;
    }

    $catalog_items = [];
    $arSelect = Array("ID", "NAME", "PROPERTY_CML2_ARTICLE", "PROPERTY_CML2_LINK", "CATALOG_GROUP_1");
    $arFilter = Array("IBLOCK_ID" => [18, 27], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $catalog_items[] = $arFields;
    }

    $arItemsToAdd = [];
    $i = 0;
    foreach ($catalog_items as $item) {
        foreach ($arArItems as $idProd_quantity) {
            if (strval($item['ID']) == $idProd_quantity[0]) {
                $arItemsToAdd[$i] = $item;
                $arItemsToAdd[$i]['QUANTITY'] = $idProd_quantity[1];
                $i++;
            }
        }
    }

    $i = 0;
    $arProdAdd = [];

    foreach ($arItemsToAdd as $item) {
        $arProdAdd[$i]['ID'] = $item['ID'];
        $arProdAdd[$i]['NAME'] = $item['NAME'];
        if ($item['PROPERTY_CML2_ARTICLE_VALUE'])
            $arProdAdd[$i]['ARTICLE'] = $item['PROPERTY_CML2_ARTICLE_VALUE'];
        else
            $iterator = CIBlockElement::GetPropertyValues(18, array('ACTIVE' => 'Y', 'ID' => $item['PROPERTY_CML2_LINK_VALUE']), false, array('ID' => 117));
        while ($row = $iterator->Fetch()) {
            $ARTICLE_ID = $row['117'];
        }
        $arProdAdd[$i]['ARTICLE'] = $ARTICLE_ID;
        $arProdAdd[$i]['PRICE'] = $item['CATALOG_PRICE_1'];
        $arProdAdd[$i]['QUANTITY'] = $item['QUANTITY'];

        $arProdAddShort[$i]['ID'] = $item['ID'];
        $arProdAddShort[$i]['QUANTITY'] = $item['QUANTITY'];
        $i++;
    }

    $strProdAddShort = implode("&", array_map(function ($a) {
        return implode("=", $a);
    }, $arProdAddShort));

    $arResult['arProdAdd'] = $arProdAdd;
    $arResult['strProdAddShort'] = $strProdAddShort;
}

/*
foreach ($arResult['GRID']['ROWS'] as $prodID => $item) {
    if ($sale = $item['PROPERTY_SALE_PRICE_VALUE']) {
        $sum_with_sale = $item['SUM_VALUE'] - ($item['SUM_VALUE'] * ($sale / 100));
        $sum_with_sale = number_format($sum_with_sale, 1, '.', ' ');
        $arResult['GRID']['ROWS'][$prodID]['SUM_WITH_SALE'] = $sum_with_sale;

        $price_with_sale = $item['PRICE_FORMATED'] - ($item['PRICE_FORMATED'] * ($sale / 100));
        $price_with_sale = number_format($price_with_sale, 1, '.', ' ');
        $arResult['GRID']['ROWS'][$prodID]['PRICE_WITH_SALE'] = $price_with_sale;

        $allSum_with_sale_value = +$sum_with_sale;
    }
}
$arResult['ALLSUM_WITH_SALE_VALUE'] = $allSum_with_sale_value;*/

