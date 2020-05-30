<?
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    use Bitrix\Main\Context;

    $request = Context::GetCurrent()->getRequest();
    $data = $request->get('data');

    $data = explode('&', $data);
    foreach ($data as $item){
        $arItem = explode('=', $item);
        $arData[] = $arItem;
    }


    if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
    {
        foreach ($arData as $prod){
            $productID = $prod[0];
            $quantity = $prod[1];
            Add2BasketByProductID(
                $productID,
                $quantity,
                array(),
                array()
            );
        }
    }


