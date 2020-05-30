<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixBasketComponent $component */

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME'],
);
$this->addExternalCss($templateData['TEMPLATE_THEME']);

$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
	"delete" => $curPage."delete&id=#ID#",
	"delay" => $curPage."delay&id=#ID#",
	"add" => $curPage."add&id=#ID#",
);
unset($curPage);

$arBasketJSParams = array(
	'SALE_DELETE' => GetMessage("SALE_DELETE"),
	'SALE_DELAY' => GetMessage("SALE_DELAY"),
	'SALE_TYPE' => GetMessage("SALE_TYPE"),
	'TEMPLATE_FOLDER' => $templateFolder,
	'DELETE_URL' => $arUrls["delete"],
	'DELAY_URL' => $arUrls["delay"],
	'ADD_URL' => $arUrls["add"],
	'EVENT_ONCHANGE_ON_START' => (!empty($arResult['EVENT_ONCHANGE_ON_START']) && $arResult['EVENT_ONCHANGE_ON_START'] === 'Y') ? 'Y' : 'N'
);
?>
<script type="text/javascript">
	var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>
</script>
<?
$APPLICATION->AddHeadScript($templateFolder."/script.js");

if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP')
{
	$APPLICATION->IncludeComponent(
		"bitrix:sale.gift.basket",
		".default", // feedback
		array(
			"SHOW_PRICE_COUNT" => 1,
			"PRODUCT_SUBSCRIPTION" => 'N',
			'PRODUCT_ID_VARIABLE' => 'id',
			"PARTIAL_PRODUCT_PROPERTIES" => 'N',
			"USE_PRODUCT_QUANTITY" => 'N',
			"ACTION_VARIABLE" => "actionGift",
			"ADD_PROPERTIES_TO_BASKET" => "Y",

			"BASKET_URL" => $APPLICATION->GetCurPage(),
			"APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
			"FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],

			"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_SHOW_VALUE"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

			'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
			'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
			'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
			'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
			'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
			'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
			'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
			'SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
			'SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
			'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
			'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
			'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
			'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
			'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],
			"LINE_ELEMENT_COUNT" => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
		),
		false
	);
}
?>
<?if($arResult['arProdAdd']):?>
	<div class="plashka-list-prods-to-add"></div>
	<div class="list-prods-to-add popup-window-container">
		<div class="popup-content">
			<table>
				<thead>
				<tr>
					<td>Артикул</td>
					<td>Наименование</td>
					<td>Цена</td>
					<td>Кол-во</td>
					<td></td>
				</tr>
				</thead>
				<tbody>
                <?foreach ($arResult['arProdAdd'] as $item):?>
					<tr data-id="<?=$item['ID']?>">
						<td><?=$item['ARTICLE']?></td>
						<td><?=$item['NAME']?></td>
						<td><?=$item['PRICE']?> руб.</td>
						<td style="text-align: center;"><?=$item['QUANTITY']?></td>
						<td>&#215;</td>
					</tr>
                <?endforeach;?>
				</tbody>
			</table>
			<button title="Закрыть" type="button" class="mfp-close"></button>
			<div class="butt" style="text-align: right;">
				<input class="btn btn-danger" id="AddProdsFromLink" type="submit" value="Добавить" data-prodsidquantity="<?=$arResult['strProdAddShort']?>">
			</div>
		</div>
	</div>
<?endif;?>
<?
if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
{
	?>
	<div id="warning_message">
		<?
		if (!empty($arResult["WARNING_MESSAGE"]) && is_array($arResult["WARNING_MESSAGE"]))
		{
			foreach ($arResult["WARNING_MESSAGE"] as $v)
				ShowError($v);
		}
		?>
	</div>
	<?

	$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
	$normalHidden = ($normalCount == 0) ? 'style="display:none;"' : '';

	$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
	$delayHidden = ($delayCount == 0) ? 'style="display:none;"' : '';

	$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
	$subscribeHidden = ($subscribeCount == 0) ? 'style="display:none;"' : '';

	$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
	$naHidden = ($naCount == 0) ? 'style="display:none;"' : '';

	?>

	<div class="basket__top">
        <a class="btn btn-default btn-delete" id="basket-delete-all" href="#"><span>Очистить</span></a>
		<a class="btn btn-default btn-delete" id="get-cart-link" href="#" style="width: 300px" onclick="ym(50108887, 'reachGoal', 'share_cart'); return true;"><span>Получить ссылку на корзину</span></a>

    </div>
    <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
        <?
        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
        ?>
    </form>
    <div class="go-back-button"><a class="btn btn-default" href="/catalog/">Продолжить покупки</a></div>


	<div class="link-basket-wrap">
	</div>
	<div class="link-basket popup-window-container">
		<div class="popup-content">
			<h2>Ссылка на корзину</h2>
			<input type="text" id="CopyCartLink">
			<p>При открытии данной ссылки у другого пользователя товары из вашей корзины будут добавлены в его корзину.</p>
			<button title="Закрыть" type="button" class="mfp-close"></button>
			<div class="butt">
				<input class="btn btn-danger" id="CopyCartLinkButton" type="submit" value="Копировать" onclick="ym(50108887, 'reachGoal', 'cart_copy'); return true;">
			</div>
		</div>
	</div>



	<?

	if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
	{
		?>
		<div style="margin-top: 35px;"><? $APPLICATION->IncludeComponent(
			"bitrix:sale.gift.basket",
			".default", // feedback
			array(
				"SHOW_PRICE_COUNT" => 1,
				"PRODUCT_SUBSCRIPTION" => 'N',
				'PRODUCT_ID_VARIABLE' => 'id',
				"PARTIAL_PRODUCT_PROPERTIES" => 'N',
				"USE_PRODUCT_QUANTITY" => 'N',
				"ACTION_VARIABLE" => "actionGift",
				"ADD_PROPERTIES_TO_BASKET" => "Y",

				"BASKET_URL" => $APPLICATION->GetCurPage(),
				"APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
				"FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],

				"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_SHOW_VALUE"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

				'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
				'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
				'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
				'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
				'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
				'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
				'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
				'SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
				'SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
				'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
				'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
				'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
				'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
				'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],

				"LINE_ELEMENT_COUNT" => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
			),
			false
		); ?>
		</div><?
	}
}
else
{
    ?>
    <div id="basket_form_container">
        <div class="float-block__wrap">
            <h2>КОРЗИНА</h2>
            <? ShowError($arResult["ERROR_MESSAGE"]); ?>
        </div>
        <div class="go-back-button"><a class="btn btn-default" href="/catalog/">Продолжить покупки</a></div>
    </div>

<?
}
?>
<script>
	var BASKET_ITEMS_COUNT = <?=$arResult['BASKET_ITEMS_COUNT']?>;

	$(document).ready(function () {
		$(document).on('click', '#get-cart-link',function () {
			var itemId = '',
				QUANTITY = '',
				link = 'https://lorian.ru/basket/import/',
				arCartImport = {};

			$('.basket__list-item').each(function () {
				itemId = $(this).children('.button-block').children('.restore-basket-row').data('item-id');
				QUANTITY = $(this).children('.coll-block').children('input').val();

				arCartImport[itemId] = QUANTITY;
			});

			var strCartImport = JSON.stringify(arCartImport);

			strCartImport = strCartImport.replace(/"/gi, '');

			$.post('/local/ajax/basket_link.php', {data: strCartImport}, function (data) {
				link = link + data + '/';

				$('.link-basket input[type="text"]').val(link);
				$('.link-basket-wrap').css('display', 'block').next('.link-basket').css('display', 'block');
			});
		});

		$(document).on('click', '.link-basket-wrap', function () {
			$(this).css('display', 'none');
			$(this).next('.link-basket').css('display', 'none');
		});
		$(document).on('click', '.link-basket .mfp-close', function () {
			$('.link-basket-wrap').css('display', 'none');
			$('.link-basket').css('display', 'none');
		});

		$('#CopyCartLinkButton').click(function(e) {
			e.preventDefault();
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($('#CopyCartLink').val()).select();
			document.execCommand("copy");
			$temp.remove();

			$(this).val('Ссылка скопирована').addClass('btn-default btn-delete').removeClass('btn-danger').css('font-size', '16px');
		});
		
		$('#AddProdsFromLink').click(function (e) {
			e.preventDefault();
			var prods = $(this).data('prodsidquantity');

			$.get('/local/ajax/addProdsFromLinkList.php', {data:prods})
				.done(function () {
					$('.list-prods-to-add').prepend('<div><p style="color: green;font-weight: 600;">Товары добавлены в корзину.</p></div>');
					window.location.href = '<?=SITE_DIR?>basket/';
				})
		});


		/*$(document).on('click', '.link-basket-wrap', function () {
			$(this).css('display', 'none');
			$(this).next('.link-basket').css('display', 'none');
		});*/
		$(document).on('click', '.list-prods-to-add .mfp-close', function () {
			$('.plashka-list-prods-to-add').css('display', 'none');
			$('.list-prods-to-add').css('display', 'none');
		});

		$(document).on('click', '.list-prods-to-add tbody td:last-child', function () {
			var id = $(this).parent('tr').data('id');
			$(this).parent('tr').fadeOut();
			var prodsidquantity = $(this).closest('.popup-content').find('#AddProdsFromLink').data('prodsidquantity');
			var arProds = prodsidquantity.split('&');
			for (var key in arProds){
				if(arProds[key].indexOf(id) === 0){
					arProds.splice(key, 1);
					break;
				}
			}
			var strProds = arProds.join('&');
			$(this).closest('.popup-content').find('#AddProdsFromLink').data('prodsidquantity', strProds);
			if(strProds.length === 0){
				$('.plashka-list-prods-to-add').css('display', 'none');
				$('.list-prods-to-add').css('display', 'none');
			}
		});
/*
		$(document).on('click', '#basket-delete-all', function () {
			$.get('/local/ajax/basket_delete_all.php')
				.done(function () {
					location.reload();
				})
		});
		*/
		/*
		$('.delete-basket-row').on('click', function (){
			var delProd = $(this).closest('.basket__list-item');
			$.get($(this).data('delete-link'))
				.done(function () {
					if(BASKET_ITEMS_COUNT === 1){

					}else{
						delProd.fadeOut();
					}
				})
		});
*/
	})
</script>
