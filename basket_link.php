<?
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    use Bitrix\Main\Context;

    $request = Context::GetCurrent()->getRequest();
    $data = $request->get('data');


    $check_name = [];
    $arSelect = Array("ID", "NAME");
    $arFilter = Array("IBLOCK_ID"=>26, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $check_name[] = $arFields['NAME'];
    }


    do{
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 50; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $loop = false;
        foreach ($check_name as $name ){
            if($name == $randomString){
                $loop = true;
            }
        }
    }while($loop);


   $el = new CIBlockElement;

    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID"      => 26,
        "NAME"           => $randomString,
        "ACTIVE"         => "Y",
        "PREVIEW_TEXT"   => $data
    );

    if($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo $randomString;
    else
        echo "Error: ".$el->LAST_ERROR;



