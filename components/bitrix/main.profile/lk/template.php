<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */


if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//Пусть для пользователей определено пользовательское свойство
// типа список с кодом UF_GENDER. Выведем отображаемое значение для
// текущего пользователя.

//Сначала получим значение пользовательского поля:
global $USER;
if(is_object($USER))
{
    $rsUser = CUser::GetList($by, $order,
        array(
            "ID" => $USER->GetID(),
        ),
        array(
            "SELECT" => array(
                "UF_STATUS",
            ),
        )
    );
    if($arUser = $rsUser->Fetch())
    {
        $rsGender = CUserFieldEnum::GetList(array(), array(
            "ID" => $arUser["UF_STATUS"],
        ));
        if($arGender = $rsGender->GetNext())
            $arGender["VALUE"];
    }
}

function user_browser($agent) {
    preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info); // регулярное выражение, которое позволяет отпределить 90% браузеров
    list(,$browser,$version) = $browser_info; // получаем данные из массива в переменную
    if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera '.$opera[1]; // определение _очень_старых_ версий Оперы (до 8.50), при желании можно убрать
    if ($browser == 'MSIE') { // если браузер определён как IE
        preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie); // проверяем, не разработка ли это на основе IE
        if ($ie) return $ie[1].' based on IE '.$version; // если да, то возвращаем сообщение об этом
        return 'IE '.$version; // иначе просто возвращаем IE и номер версии
    }
    if ($browser == 'Firefox') { // если браузер определён как Firefox
        preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff); // проверяем, не разработка ли это на основе Firefox
        if ($ff) return $ff[1].' '.$ff[2]; // если да, то выводим номер и версию
    }
    if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5); // если браузер определён как Opera 9.80, берём версию Оперы из конца строки
    if ($browser == 'Version') return 'Safari '.$version; // определяем Сафари
    if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko'; // для неопознанных браузеров проверяем, если они на движке Gecko, и возращаем сообщение об этом
    return $browser.' '.$version; // для всех остальных возвращаем браузер и версию
}

CModule::IncludeModule("iblock");
$CATEGORY1 = array();
$COUNTRY_ARRAY_RU = array();
$SUBJECT_ARRAY = array();
$CATEGORY_RUS = array();
$CATEGORY_ENG = array();
$TRANSPORT_RUS = array();
$TRANSPORT_ENG = array();


global $USER;
$arFilter = array("ID" => $USER->GetID());
$arParams["SELECT"] = array("UF_STATUS");
$arRes = CUser::GetList($by,$desc,$arFilter,$arParams);
if ($res = $arRes->Fetch()) {
    foreach ($res["UF_STATUS"] as $id) {
        $rsRes= CUserFieldEnum::GetList(array(), array(
            "ID" => $id,
        ));
        if($arGender = $rsRes->GetNext())
            echo $arGender["VALUE"];
    }
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 5, 'ACTIVE' => 'Y', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$TRANSPORT_RUS[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 5, 'ACTIVE' => 'N', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$TRANSPORT_ENG[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'ACTIVE' => 'Y', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$CATEGORY_RUS[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'ACTIVE' => 'N', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $CATEGORY_ENG[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockElement::GetList(array("NAME" => "ASC"),array("IBLOCK_ID" => 3), false, false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $SUBJECT_ARRAY[] = $ar_res['NAME'];
}

$res = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 2), false, false, array('ID','NAME',"PROPERTY_country_id"));
while($ar_res = $res -> GetNext()){
    $COUNTRY_ARRAY_RU[$ar_res['PROPERTY_COUNTRY_ID_VALUE']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $CATEGORY1[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 6, 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $HOTEL[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 5, 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $TRANSPORT[$ar_res['ID']] = $ar_res['NAME'];
}

if($arResult['arUser']['PERSONAL_GENDER'] == "M"){
	$arResult['arUser']['PERSONAL_GENDER'] = "Мужской";
}
if($arResult['arUser']['PERSONAL_GENDER'] == "F"){
	$arResult['arUser']['PERSONAL_GENDER'] = "Женский";
}
if($_POST['DATE_IN']){
	$arParameters['SELECT'] = array("UF_DATE_IN","UF_DATE_OUT","UF_FIO");
	$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("UF_ORG_ID" => $arResult['arUser']['UF_ORG_ID']),$arParameters); // выбираем пользователей
	$user = new CUser;
	while($arUser = $rsUsers -> GetNext()){
		$user->Update($arUser['ID'], array('UF_DATE_IN' => $_POST['DATE_IN']));
	}
}
if($_POST['DATE_OUT']){
	$arParameters['SELECT'] = array("UF_DATE_IN","UF_DATE_OUT","UF_FIO");
	$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("UF_ORG_ID" => $arResult['arUser']['UF_ORG_ID']),$arParameters); // выбираем пользователей
	$user = new CUser;
	while($arUser = $rsUsers -> GetNext()){
		$user->Update($arUser['ID'], array('UF_DATE_OUT' => $_POST['DATE_OUT']));
	}
}
?>
<ul class="side-nav">
	<li class="active"><a data-element-id="item1" href="javascript:void(0)"><span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Личные данные</span><span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Personal data</span></a></li>
	<?if (array_key_exists('mospan', $_GET)):?>
	<li><a data-element-id="item7" href="javascript:void(0)">Спортивные мероприятия</a></li>
	<?endif?>
	<li><a data-element-id="logout" href="/?logout=Yes"><span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Выход</span><span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Quit</span></a></li>
</ul>
<div class="reg-form">
    <input id="rulang" type="button" value="RU">
    <input id="enlang" type="button" value="EN">
	<h2 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Личный кабинет</h2>
    <h2 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Profile</h2>
	<?//if($_GET['Y']!='Y'):?>
		<!--Личный кабинет на данный момент не доступен. Приносим свои извенения.-->
	<?//else:?>
	<?//pr($arResult);?>
	<div data-element-id="item1" class="form-box">
        <?if($arGender["VALUE"] == 'ожидает подтверждения'):?>
            <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Персональные данные</h3>
            <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Personal Information</h3>
            <a id="edit-live" href="javascript:void(0)"><span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Edit profile</span><span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Редактировать</span></a>
            <form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
                <?=$arResult["BX_SESSION_CHECK"]?>
                <input type="hidden" name="lang" value="<?=LANG?>" />
                <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
                <input type="hidden" name="LOGIN" value="<?echo $arResult["arUser"]["LOGIN"]?>" />
                <input type="hidden" name="EMAIL" value="<?echo $arResult["arUser"]["EMAIL"]?>" />
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Статус участника:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Status:</dt>
                    <dd class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>" id="userStatus"></dd>
                    <dd class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>"><?=$arGender["VALUE"]?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Личное фото:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Personal photo:</dt>
                    <dd>
                    <span>
                        <div class="image">
                            <?=$arResult['arUser']['PERSONAL_PHOTO_HTML']?>
                        </div>
                    </span>
                        <span class="hidden" style="width:240px">
                            <input type="file" name="PERSONAL_PHOTO" value="<?=$arResult['arUser']['PERSONAL_PHOTO']?>" />
                    </span>
                    </dd>
                    <dd>

                    </dd>
                    <dt>ID:</dt>
                    <dd><?=$arResult['arUser']['ID']?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Фамилия:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Last name:</dt>
                    <dd><span><?=$arResult['arUser']['LAST_NAME']?></span>
                        <span class="hidden" style="width:240px">
                        <input class="text" type="text" name="LAST_NAME" value="<?=$arResult['arUser']['LAST_NAME']?>" />
                    </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Имя:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Name:</dt>
                    <dd><span><?=$arResult['arUser']['NAME']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="NAME" value="<?=$arResult['arUser']['NAME']?>" />
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Отчество:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Middle name:</dt>
                    <dd><span><?=$arResult['arUser']['SECOND_NAME']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="SECOND_NAME" value="<?=$arResult['arUser']['SECOND_NAME']?>" />
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">ФИО на латинском:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Name on badge:</dt>
                    <dd><span><?=$arResult['arUser']['UF_FIO_EN']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="UF_FIO_EN" value="<?=$arResult['arUser']['UF_FIO_EN']?>" />
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Дата рождения:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Date of birth:</dt>
                    <dd><span><?=$arResult['arUser']['PERSONAL_BIRTHDAY']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="PERSONAL_BIRTHDAY" value="<?=$arResult['arUser']['PERSONAL_BIRTHDAY']?>">
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Категория участника:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Category:</dt>
                    <dd><span><?=$arResult['arUser']['UF_CAT1']?></span>
                        <span class="hidden" style="width:240px">
                        <select name="UF_CAT1" class="select-large selectcat">
							<option value="<?=$arResult['arUser']['UF_CAT1']?>"><?=$arResult['arUser']['UF_CAT1']?></option>
                            <?foreach($CATEGORY_RUS as $k => $v):?>
								<option class="ruscat" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
                            <?foreach($CATEGORY_ENG as $k => $v):?>
                               <option class="engcat" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </span>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Информация об организации</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Organization information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Организация:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Organization / Company:</dt>
                    <dd><span><?=$arResult['arUser']['WORK_COMPANY']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="WORK_COMPANY" value="<?=$arResult['arUser']['WORK_COMPANY']?>">
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Должность:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Position:</dt>
                    <dd><span><?=$arResult['arUser']['WORK_POSITION']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="WORK_POSITION" value="<?=$arResult['arUser']['WORK_POSITION']?>">
                </span>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Проживание</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Accommodation</h3>
                <dl class="personal-info">

				<dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Город прибытия:</dt>
				<dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Arrival city:</dt>
				<dd><span><?=$arResult['arUser']['UF_ARRIVAL_CITY']?></span>
				<span class="hidden" style="width:240px">
					<select name="UF_ARRIVAL_CITY" class="select-large selectarrcity">
						<option value="<?=$arResult['arUser']['UF_ARRIVAL_CITY']?>"><?=$arResult['arUser']['UF_ARRIVAL_CITY']?></option>
						<option class="rusarrcity" value="Кемерово">Кемерово</option>
						<option class="rusarrcity" value="Новокузнецк">Новокузнецк</option>
						<option class="rusarrcity" value="Новосибирск">Новосибирск</option>
						<option class="rusarrcity" value="Томск">Томск</option>
					</select>
				</span>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Гостиница:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Hotel:</dt>
                    <dd><span><?=$arResult['arUser']['UF_LIVING_PLACE']?></span>
                        <span class="hidden" style="width:240px">
                        <select name="UF_LIVING_PLACE" class="select-large">
							<option value="<?=$arResult['arUser']['UF_LIVING_PLACE']?>"><?=$arResult['arUser']['UF_LIVING_PLACE']?></option>
							<?foreach($HOTEL as $k => $v):?>
                                <option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </span>
                    </dd>
                    <dt>Дата прибытия</dt>
                    <dd>
                        <span><?=$arResult['arUser']['UF_DATE_IN']?></span>
                        <span class="hidden" style="width:240px">
                            <input type="text" id="date" name="UF_DATE_IN" value="<?=$arResult['arUser']['UF_DATE_IN']?>" />
                        </span>
                    </dd>
                    <dt>Дата отъезда</dt>
                    <dd>
                        <span><?=$arResult['arUser']['UF_DATE_OUT']?></span>
                        <span class="hidden" style="width:240px">
                            <input type="text" id="date2" name="UF_DATE_OUT" value="<?=$arResult['arUser']['UF_DATE_OUT']?>" />
                        </span>
                    </dd>
                    <dt>Вид транспорта</dt>
                    <dd>
                        <span>
                            <?=$arResult['arUser']['UF_TRANSPORT']?>
                        </span>
                        <span class="hidden" style="width:240px">
                        <select name="UF_TRANSPORT" class="select-large selecttransport">
							<option value="<?=$arResult['arUser']['UF_TRANSPORT']?>"><?=$arResult['arUser']['UF_TRANSPORT']?></option>
							<?foreach($TRANSPORT_RUS as $k => $v):?>
                                <option class="rustr" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
                            <?foreach($TRANSPORT_ENG as $k => $v):?>
                                <option class="engtr" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </span>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Контактная информация</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Contact Information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Электронная почта:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">E-mail:</dt>
                    <dd><?=$arResult['arUser']['EMAIL']?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Мобильный телефон:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Mobile phone number:</dt>
                    <dd><span><?=$arResult['arUser']['PERSONAL_MOBILE']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="PERSONAL_MOBILE" value="<?=$arResult['arUser']['PERSONAL_MOBILE']?>">
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Страна:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Country:</dt>
                    <dd><span><?=$arResult['arUser']['UF_REG_COUNTRY']?></span>
                        <span class="hidden" style="width:240px">
                         <select name="UF_REG_COUNTRY" class="select-large">
                             <option value="<?=$arResult['arUser']['UF_REG_COUNTRY']?>"><?=$arResult['arUser']['UF_REG_COUNTRY']?></option>
                        <?foreach($COUNTRY_ARRAY_RU as $k => $v):?>
                            <option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                    </select>
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Регион:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Region:</dt>
                    <dd><span><?=$arResult['arUser']['UF_REGION']?></span>
                        <span class="hidden" style="width:240px">
                    <select name="UF_REGION" class="select-large">
                        <option value="<?=$arResult['arUser']['UF_REGION']?>"><?=$arResult['arUser']['UF_REGION']?></option>
                        <?foreach($SUBJECT_ARRAY as $k => $v):?>
                            <option value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                    </select>
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Город:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">City:</dt>
                    <dd><span><?=$arResult['arUser']['UF_CITY']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="UF_CITY" value="<?=$arResult['arUser']['UF_CITY']?>">
                </span>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Место проживания:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Place of residence:</dt>
                    <dd><span><?=$arResult['arUser']['UF_ADDRESS']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="UF_ADDRESS" value="<?=$arResult['arUser']['UF_ADDRESS']?>">
                </span>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Паспортные данные</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Passport information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Серия и номер паспорта:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Passport number:</dt>
                    <dd><span><?=$arResult['arUser']['UF_PASSPORT']?></span>
                        <span class="hidden" style="width:240px">
                    <input class="text" type="text" name="UF_PASSPORT" value="<?=$arResult['arUser']['UF_PASSPORT']?>">
                </span>
                    </dd>
                </dl>
                <span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">
                    <input type="submit" class="submit hidden" name="save" value="Сохранить изменения">
                </span>
                <span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">
                    <input type="submit" class="submit hidden" name="save" value="Save changes">
                </span>

            </form>
        <?else:?>
            <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Персональные данные</h3>
            <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Personal Information</h3>
            <a id="edit-live" href="javascript:void(0)"><span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Edit profile</span><span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Редактировать</span></a>
            <form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
                <?=$arResult["BX_SESSION_CHECK"]?>
                <input type="hidden" name="lang" value="<?=LANG?>" />
                <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
                <input type="hidden" name="LOGIN" value="<?echo $arResult["arUser"]["LOGIN"]?>" />
                <input type="hidden" name="EMAIL" value="<?echo $arResult["arUser"]["EMAIL"]?>" />
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Статус участника:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Status:</dt>
                    <dd class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>" id="userStatus"></dd>
                    <dd class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>"><?=$arGender["VALUE"]?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Личное фото:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Personal photo:</dt>
                    <dd>
                    <span>
                        <div class="image">
                            <?=$arResult['arUser']['PERSONAL_PHOTO_HTML']?>
                        </div>
                    </span>
                        <span class="hidden" style="width:240px">
                            <input type="file" name="PERSONAL_PHOTO" value="<?=$arResult['arUser']['PERSONAL_PHOTO']?>" />
                    </span>
                    </dd>
                    <dd>

                    </dd>
                    <dt>ID:</dt>
                    <dd><?=$arResult['arUser']['ID']?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Фамилия:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Last name:</dt>
                    <dd>
                        <?=$arResult['arUser']['LAST_NAME']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Имя:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Name:</dt>
                    <dd>
                        <?=$arResult['arUser']['NAME']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Отчество:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Middle name:</dt>
                    <dd>
                        <?=$arResult['arUser']['SECOND_NAME']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">ФИО на латинском:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Name on badge:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_FIO_EN']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Дата рождения:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Date of birth:</dt>
                    <dd>
                        <?=$arResult['arUser']['PERSONAL_BIRTHDAY']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Категория участника:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Category:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_CAT1']?>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Информация об организации</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Organization information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Организация:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Organization / Company:</dt>
                    <dd>
                        <?=$arResult['arUser']['WORK_COMPANY']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Должность:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Position:</dt>
                    <dd>
                        <?=$arResult['arUser']['WORK_POSITION']?>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Проживание</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Accommodation</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Гостиница:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Hotel:</dt>
                    <dd><span><?=$arResult['arUser']['UF_LIVING_PLACE']?></span>
                        <span class="hidden" style="width:240px">
                        <select name="UF_LIVING_PLACE" class="select-large">
							<option value="<?=$arResult['arUser']['UF_LIVING_PLACE']?>"><?=$arResult['arUser']['UF_LIVING_PLACE']?></option>
							<?foreach($HOTEL as $k => $v):?>
                                <option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </span>
                    </dd>
                    <dt>Дата прибытия</dt>
                    <dd>
                        <span><?=$arResult['arUser']['UF_DATE_IN']?></span>
                        <span class="hidden" style="width:240px">
                            <input type="text" id="date" name="UF_DATE_IN" value="<?=$arResult['arUser']['UF_DATE_IN']?>" />
                        </span>
                    </dd>
                    <dt>Дата отъезда</dt>
                    <dd>
                        <span><?=$arResult['arUser']['UF_DATE_OUT']?></span>
                        <span class="hidden" style="width:240px">
                            <input type="text" id="date2" name="UF_DATE_OUT" value="<?=$arResult['arUser']['UF_DATE_OUT']?>" />
                        </span>
                    </dd>
                    <dt>Вид транспорта</dt>
                    <dd>
                        <span>
                            <?=$arResult['arUser']['UF_TRANSPORT']?>
                        </span>
                        <span class="hidden" style="width:240px">
                        <select name="UF_TRANSPORT" class="select-large">
							<option value="<?=$arResult['arUser']['UF_TRANSPORT']?>"><?=$arResult['arUser']['UF_TRANSPORT']?></option>
							<?foreach($TRANSPORT as $k => $v):?>
                                <option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </span>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Контактная информация</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Contact Information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Электронная почта:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">E-mail:</dt>
                    <dd><?=$arResult['arUser']['EMAIL']?></dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Мобильный телефон:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Mobile phone number:</dt>
                    <dd>
                        <?=$arResult['arUser']['PERSONAL_MOBILE']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Страна:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Country:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_REG_COUNTRY']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Регион:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Region:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_REGION']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Город:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">City:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_CITY']?>
                    </dd>
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Место проживания:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Place of residence:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_ADDRESS']?>
                    </dd>
                </dl>
                <h3 class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Паспортные данные</h3>
                <h3 class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Passport information</h3>
                <dl class="personal-info">
                    <dt class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">Серия и номер паспорта:</dt>
                    <dt class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">Passport number:</dt>
                    <dd>
                        <?=$arResult['arUser']['UF_PASSPORT']?>
                    </dd>
                </dl>

                <span class="rus <?=$arResult['arUser']['UF_RUS_LANG']?>">
                    <input type="submit" class="submit hidden" name="save" value="Сохранить изменения">
                </span>
                <span class="eng <?=$arResult['arUser']['UF_ENG_LANG']?>">
                    <input type="submit" class="submit hidden" name="save" value="Save changes">
                </span>
            </form>
        <?endif?>



	</div>
	<div data-element-id="item2" style="display:none" class="form-box">
		<h3>Проживание <a id="edit-live" href="javascript:void(0)">Редактировать</a></h3>
		<dl class="personal-info">
			<dt>Название гостиницы</dt>
			<dd><?=$arResult['arUser']['WORK_COMPANY']?></dd>
			<dt>Должность</dt>
			<dd><?=$arResult['arUser']['WORK_POSITION']?></dd>
		</dl>
        <input type="submit" class="submit hidden" name="save" value="Сохранить изменения">
		<?if($arResult['arUser']['UF_UPOLN']):?>
		<div class="row">
			<dl class="personal-info">
				<dt>Дата прибытия</dt>
				<dd>
				<form id="DATE_IN_form" method="post">
					<select name="d1">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m1'>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y1'>
						<option value="<?=date('Y')?>"><?=date('Y')?></option>
						<option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
					</select>
					<a id="DATE_IN_action" href="javascript:void(0)">Назначить всем сотрудникам</a>
					<input type="hidden" name="DATE_IN" />
				</form>
				</dd>
			</dl>
		</div>
		<div class="row">
			<dl class="personal-info">
				<dt>Дата отправления</dt>
				<dd>
				<form id="DATE_OUT_form" method="post">
					<select name="d2">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m2'>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y2'>
						<option value="<?=date('Y')?>"><?=date('Y')?></option>
						<option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
					</select>
					<a id="DATE_OUT_action" href="javascript:void(0)">Назначить всем сотрудникам</a>
					<input type="hidden" name="DATE_OUT" />
				</form>
				</dd>
			</dl>
		</div><br/>
		<table class="info-table">
			<thead>
				<tr>
					<td>ФИО</td>
					<td>Должность</td>
					<td>Дата приезда</td>
					<td>Дата выезда</td>
					<td>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
			<?
				$arParameters['SELECT'] = array("ID","UF_DATE_IN","UF_DATE_OUT","UF_FIO","UF_ORG_ID");
				if($arResult['arUser']['UF_ORG_ID']){
					$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("UF_ORG_ID" => $arResult['arUser']['UF_ORG_ID']), $arParameters); // выбираем пользователей
					while($arUser = $rsUsers -> GetNext()){
						//pr($arUser);
					?>
					<tr>
						<td><?=$arUser["UF_FIO"];?></td>
						<td><?=$arUser["WORK_POSITION"];?></td>
						<td><?=$arUser["UF_DATE_IN"];?></td>
						<td><?=$arUser["UF_DATE_OUT"];?></td>
						<td><a href="#popup<?=$arUser["ID"]?>" class="fancybox">подробнее..</a></td>
					</tr>
					<?
					}
				}
			?>
			</tbody>
		</table>
		<?endif?>
		<a href="/upload/doverennost.doc">Доверенность на получение аккредитации</a>
		<?if($arResult['arUser']['UF_UPOLN']):?>
			<br>
			<br>
			<form method="get" action="/register/">
				<input type="hidden" name="new" value="Y" />
				<button class="submit">Добавить сотрудника</button>
			</form>
		<?endif?>
	</div>
	<div data-element-id="item3" style="display:none" class="form-box">
		<h3>Детали перелета/проезда <a id="edit-live" href="javascript:void(0)">Редактировать</a></h3>
		<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
			<input type="hidden" name="LOGIN" value="<?echo $arResult["arUser"]["LOGIN"]?>" />
			<input type="hidden" name="EMAIL" value="<?echo $arResult["arUser"]["EMAIL"]?>" />
			<dl class="personal-info">
				<dt>Вид транспорта</dt>
				<dd><span><?=$arResult['arUser']['UF_TRANSPORT']?></span>
					<span class="hidden" style="width:240px">
						<select value="<?=$arResult['arUser']['UF_TRANSPORT']?>" name="UF_TRANSPORT">
							<option <?if($arResult['arUser']['UF_TRANSPORT'] == 'Самолёт')echo 'selected '?>value="Самолёт">Самолёт</option>
							<option <?if($arResult['arUser']['UF_TRANSPORT'] == 'Поезд')echo 'selected '?>value="Поезд">Поезд</option>
							<option <?if($arResult['arUser']['UF_TRANSPORT'] == 'Личный автомобиль')echo 'selected '?>value="Личный автомобиль">Личный автомобиль</option>
							<option <?if($arResult['arUser']['UF_TRANSPORT'] == 'Транспорт не требуется')echo 'selected '?>value="Транспорт не требуется">Транспорт не требуется</option>
						</select>
					</span>
				</dd>
				<span id="UF_TRANSPORT" style="display:none">
					<dt>Гос номер</dt>
					<dd><span><?=$arResult['arUser']['UF_AUTO_NUM']?></span><span class="hidden"><input type="text" name="UF_AUTO_NUM" value="<?=$arResult['arUser']['UF_AUTO_NUM']?>" /></span></dd>
				</span>
				<dt>Дата прибытия</dt>
				<dd><span><?=$arResult['arUser']['UF_DATE_IN']?></span><span class="hidden"><input type="text" name="UF_DATE_IN" value="<?=$arResult['arUser']['UF_DATE_IN']?>" /></span></dd>
				<dt>Город прибытия</dt>
				<dd><span><?=$arResult['arUser']['UF_CITY_IN']?></span><span class="hidden"><input type="text" name="UF_CITY_IN" value="<?=$arResult['arUser']['UF_CITY_IN']?>" /></span></dd>
				<dt>Номер рейса/поезда</dt>
				<dd><span><?=$arResult['arUser']['UF_REYS']?></span><span class="hidden"><input type="text" name="UF_REYS" value="<?=$arResult['arUser']['UF_REYS']?>" /></span></dd>
				<dt>Время прилета/приезда</dt>
				<dd><span><?=$arResult['arUser']['UF_TIME_IN']?></span><span class="hidden"><input type="text" name="UF_TIME_IN" value="<?=$arResult['arUser']['UF_TIME_IN']?>" /></span></dd>
				<dt>Дата отправления</dt>
				<dd><span><?=$arResult['arUser']['UF_DATE_OUT']?></span><span class="hidden"><input type="text" name="UF_DATE_OUT" value="<?=$arResult['arUser']['UF_DATE_OUT']?>" /></span></dd>
				<dt>Город отправления</dt>
				<dd><span><?=$arResult['arUser']['UF_CITY_OUT']?></span><span class="hidden"><input type="text" name="UF_CITY_OUT" value="<?=$arResult['arUser']['UF_CITY_OUT']?>" /></span></dd>
				<dt>Номер рейса/поезда</dt>
				<dd><span><?=$arResult['arUser']['UF_REYS2']?></span><span class="hidden"><input type="text" name="UF_REYS2" value="<?=$arResult['arUser']['UF_REYS2']?>" /></span></dd>
				<dt>Время вылета/выезда</dt>
				<dd><span><?=$arResult['arUser']['UF_TIME_OUT']?></span><span class="hidden"><input type="text" name="UF_TIME_OUT" value="<?=$arResult['arUser']['UF_TIME_OUT']?>" /></span></dd>
			</dl>
			<input type="submit" class="submit hidden" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">
		</form>
	</div>
	<div data-element-id="item4" style="display:none" class="form-box">
		<?if ($arResult['arUser']['UF_LIVE']):
			$LIVE = array();
			$res = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 6, 'ID' => $arResult['arUser']['UF_LIVE']), false, false, array('ID', 'NAME', 'DETAIL_TEXT', 'PROPERTY_SITE', 'PROPERTY_ADDR', 'PROPERTY_file', 'PROPERTY_photos'));
			$LIVE['PHOTOS'] = array();
			$LIVE['FILE'] = '';
			while ($ar_res = $res -> GetNext()){
				$LIVE['NAME'] = $ar_res['NAME'];
				$LIVE['SITE'] = $ar_res['PROPERTY_SITE_VALUE'];
				$LIVE['ADDR'] = $ar_res['PROPERTY_ADDR_VALUE'];
				$LIVE['DESCRIPTION'] = $ar_res['DETAIL_TEXT'];
				if (trim($ar_res['PROPERTY_FILE_VALUE']) != '') {
					$LIVE['FILE'] = CFile::GetPath($ar_res['PROPERTY_FILE_VALUE']);
				}
				if (!is_array($ar_res['PROPERTY_PHOTOS_VALUE']) && trim($ar_res['PROPERTY_PHOTOS_VALUE']) != '') {
					$ar_res['PROPERTY_PHOTOS_VALUE'] = array($ar_res['PROPERTY_PHOTOS_VALUE']);
				}
				if (is_array($ar_res['PROPERTY_PHOTOS_VALUE'])) {
					foreach ($ar_res['PROPERTY_PHOTOS_VALUE'] as $photoId) {
						$thumbImage = CFile::ResizeImageGet($photoId, array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT);
						$fullImage = CFile::ResizeImageGet($photoId, array('width' => 1000, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL);
						$LIVE['PHOTOS'][] = array('thumb' => $thumbImage['src'], 'full' => $fullImage['src']);
					}
				}
			}
			
			$LIVE['VOUCHER'] = '';
			if ($arResult['arUser']['UF_VOUCHER']) {
				$LIVE['VOUCHER'] = CFile::GetPath($arResult['arUser']['UF_VOUCHER']);
			}
		?>
			<h3>Гостиница</h3>
			<dl class="personal-info">
				<dt>Название</dt>
				<dd><?=$LIVE['NAME']?></dd>
				<dt>Сайт</dt>
				<dd><a target="_blank" href="<?=$LIVE['SITE']?>"><?=$LIVE['SITE']?></a></dd>
				<dt>Адрес</dt>
				<dd><address><?=$LIVE['ADDR']?></address></dd>
				<dt>О гостинице</dt>
				<dd><?=$LIVE['DESCRIPTION']?></dd>
				<?if (!empty($LIVE['PHOTOS'])):?>
				<dt>Фотографии</dt>
				<dd>
					<?foreach ($LIVE['PHOTOS'] as $photo):?>
					<a rel="hotel-photos" href="<?=$photo['full']?>"><img src="<?=$photo['thumb']?>"></a>
					<?endforeach?>
				</dd>
				<?endif?>
				<?if (trim($LIVE['FILE']) != ''):?>
				<dt>Заявка на бронирование</dt>
				<dd><a href="<?=$LIVE['FILE']?>">Скачать</a></dd>
				<?endif?>
				<?if (trim($LIVE['VOUCHER']) != ''):?>
				<dt>Файл для скачивания 2</dt>
				<dd><a href="<?=$LIVE['VOUCHER']?>">Скачать</a></dd>
				<?endif?>
				<?if (trim($LIVE['VOUCHER']) != ''):?>
				<dt>Общая информация</dt>
				<dd><a href="<?=$LIVE['VOUCHER']?>">Скачать</a></dd>
				<?endif?>
			</dl>
		<?endif;?>
		<?if($arResult['arUser']['UF_COMMENT']):?><h4>Комментарий организатора</h4>
			<span class="comment"><?=$arResult['arUser']['UF_COMMENT']?></span>
		<?endif;?>
	</div>
	<div data-element-id="item5" style="display:none" class="form-box">
		<h3>Информация по Форуму</h3>
		<ul class="programm">
			<?
			$arrFilter = array();
			$arrFilter["IBLOCK_ID"] = 7;
			$arrFilter[] = array(
			"LOGIC" => "OR",
				array("PROPERTY_SHOW_USER" => $arResult['arUser']['ID']),
				array("=PROPERTY_SHOW_ALL" => 1)
			);
			//pr($arrFilter);
			$res = CIBlockElement::GetList(array(),$arrFilter, false, false, array('ID','NAME','PROPERTY_FILE','DETAIL_TEXT','PREVIEW_PICTURE'));
			while($ar_res = $res -> GetNext()){
			?>
				<?$file = CFile::ResizeImageGet($ar_res['PREVIEW_PICTURE'], array('width'=>35, 'height'=>35), BX_RESIZE_IMAGE_EXACT);?>
				<li><img src="<?=$file['src']?>" height="35" width="35" alt=""><span><?=$ar_res['NAME']?> <a href="<?=CFile::GetPath($ar_res['PROPERTY_FILE_VALUE'])?>">скачать/просмотреть</a></span></li>
			<?
			}
			?>
		</ul>
	</div>
	<div data-element-id="item6" style="display:none" class="form-box">
		<h3>Деловая программа Форума</h3>
		<p>Предлагаем Вашему вниманию Деловую программу форума. Просим отметить, какие мероприятия вы планируете посетить.</p>
		<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"voting", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "N",
		"COMPONENT_TEMPLATE" => "voting",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "PREVIEW_TEXT",
			1 => "ACTIVE_FROM",
			2 => "ACTIVE_TO",
			3 => "",
		),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "40",
		"IBLOCK_TYPE" => "for_lk",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "1000",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Деловая программа Форума",
		"PARENT_SECTION" => "119",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC"
	),
	false
);?>
	</div>
	<div data-element-id="item7" style="display:none" class="form-box">
		<h3>Спортивные мероприятия</h3>
		
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"voting",
			Array(
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"ADD_SECTIONS_CHAIN" => "N",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"AJAX_OPTION_HISTORY" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "Y",
				"CACHE_TIME" => "36000000",
				"CACHE_TYPE" => "N",
				"CHECK_DATES" => "N",
				"COMPONENT_TEMPLATE" => "voting",
				"DETAIL_URL" => "",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"DISPLAY_DATE" => "N",
				"DISPLAY_NAME" => "Y",
				"DISPLAY_PICTURE" => "N",
				"DISPLAY_PREVIEW_TEXT" => "N",
				"DISPLAY_TOP_PAGER" => "N",
				"FIELD_CODE" => array("ACTIVE_FROM", "ACTIVE_TO", "PREVIEW_TEXT"),
				"FILTER_NAME" => "",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"IBLOCK_ID" => "40",
				"IBLOCK_TYPE" => "for_lk",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"INCLUDE_SUBSECTIONS" => "N",
				"MESSAGE_404" => "",
				"NEWS_COUNT" => "1000",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => ".default",
				"PAGER_TITLE" => "Спортивные мероприятия",
				"PARENT_SECTION" => "120",
				"PARENT_SECTION_CODE" => "",
				"PREVIEW_TRUNCATE_LEN" => "",
				"PROPERTY_CODE" => array("", ""),
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_STATUS_404" => "N",
				"SET_TITLE" => "N",
				"SHOW_404" => "N",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_BY2" => "SORT",
				"SORT_ORDER1" => "ASC",
				"SORT_ORDER2" => "ASC"
			)
		);?>
	</div>
	<?//endif;?>
</div>


<script>

    let userStatus = '<?=$arGender["VALUE"]?>';
    switch (userStatus) {
        case 'заявка отклонена':
            userStatus = 'application is declined';
            break;
        case 'заявка одобрена':
            userStatus = 'application is approved';
            break;
        case 'ожидает подтверждения':
            userStatus = 'application is waiting for confirmation';
            break;
    }
    document.getElementById("userStatus").innerHTML = userStatus;

    jQuery(function($){
        $("#date").mask("99.99.9999");
        $("#date2").mask("99.99.9999");
    });

    $(document).ready(function(){
        let cacheDomRuVac = $('.rusvac');
        let cacheDomEnVac = $('.engvac');
        let cacheDomRuCat = $('.ruscat');
        let cacheDomEnCat = $('.engcat');
        let cacheDomRuTr = $('.rustr');
        let cacheDomEnTr = $('.engtr');
        let rusHidden = "<?=$arResult['arUser']['UF_RUS_LANG']?>";
        let engHidden = "<?=$arResult['arUser']['UF_ENG_LANG']?>";

        if(rusHidden == "hidden") {
            $('.rusvac').remove();
            $('.ruscat').remove();
            $('.rustr').remove();
        }
        if(engHidden == "hidden") {
            $('.engvac').remove();
            $('.engcat').remove();
            $('.engtr').remove();
        }

        $('#enlang').click(function(){
            $('.rus').addClass('hidden');
            $('.eng').removeClass('hidden');
            cacheDomRuVac.remove();
            $('.vaccine').append(cacheDomEnVac);
            cacheDomRuCat.remove();
            $('.selectcat').append(cacheDomEnCat);
            cacheDomRuTr.remove();
            $('.selecttransport').append(cacheDomEnTr);
        });

        $('#rulang').click(function(){
            $('.eng').addClass('hidden');
            $('.rus').removeClass('hidden');
            cacheDomEnVac.remove();
            $('.vaccine').append(cacheDomRuVac);
            cacheDomEnCat.remove();
            $('.selectcat').append(cacheDomRuCat);
            cacheDomEnTr.remove();
            $('.selecttransport').append(cacheDomRuTr);
        });
    });



    $('[name=UF_VACCINE]').change(function() {
        if($(this).find("option:selected").val() == "Вакцинирован") {
            $(".scan").show();
        } else if ($(this).find("option:selected").val() == "Справка о медицинском отводе от прививки") {
            $(".scan").show();
        } else if ($(this).find("option:selected").val() == "Первый этап вакцинации пройден") {
            $(".scan").show();
        } else {
            $(".scan").hide();
        };
    });
	$(document).ready(function(){
		$("a[rel=hotel-photos]").fancybox();

		$(".fancybox").fancybox();
		
		if($('[name=UF_TRANSPORT]').val() == 'Личный автомобиль'){
			$('#UF_TRANSPORT').show();
		}
		$('[name=UF_TRANSPORT]').change(function(){
			if($(this).val() == 'Личный автомобиль'){
				$('#UF_TRANSPORT').show();
			}
			else {
				$('#UF_TRANSPORT').hide();
				$('[name=UF_AUTO_NUM]').val('');
			}
		});
	});
	$('.side-nav a').click(function(){
		if($(this).attr('data-element-id') != 'logout'){
			var el = $(this);
			$('.side-nav li').each(function(){
				$(this).removeClass('active');
			});
			el.parent().addClass('active');
			$('.form-box').each(function(){
				if(el.attr('data-element-id') == $(this).attr('data-element-id'))$(this).show();
				else $(this).hide();
			});
		}
	});
	$('#edit-live').click(function(){
		$('[name=form1] dd span').toggleClass('hidden');
		$('[name=form1] .submit').toggleClass('hidden');
	});
	
	$('#DATE_IN_action').click(function(){
		var date_in = $('[name=d1]').val() + '.' + $('[name=m1]').val() + '.' + $('[name=y1]').val();
		$('[name=DATE_IN]').val(date_in);
		$('#DATE_IN_form').submit();
	});
	$('#DATE_OUT_action').click(function(){
		var date_in = $('[name=d2]').val() + '.' + $('[name=m2]').val() + '.' + $('[name=y2]').val();
		$('[name=DATE_OUT]').val(date_in);
		$('#DATE_OUT_form').submit();
	});
</script>

<?
$arParameters['SELECT'] = array("UF_*");
$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("UF_ORG_ID" => $arResult['arUser']['UF_ORG_ID']),$arParameters); // выбираем пользователей
while($arUser = $rsUsers -> GetNext()){
	//pr($arUser);
?>
<?if($arResult['arUser']['WORK_COMPANY']):?>
<div class="reg-form" style="display: none; margin: 0;" id="popup<?=$arUser['ID']?>">
	<div class="visible">
		<dl class="personal-info">
			<dt>ФИО:</dt>
			<dd><?=$arUser['UF_FIO']?></dd>
			<dt>ФИО на латинском:</dt>
			<dd><?=$arUser['UF_FIO_EN']?></dd>
			<dt>Дата рождения:</dt>
			<dd><?=$arUser['PERSONAL_BIRTHDAY']?></dd>
			<dt>Пол:</dt>
			<dd><?if($arUser['PERSONAL_GENDER'] == 'M')echo "мужской";else echo "женский";?></dd>
			<dt>Мобильный телефон</dt>
			<dd><?=$arUser['PERSONAL_MOBILE']?></dd>
			<dt>Электронная почта:</dt>
			<dd><?=$arUser['EMAIL']?></dd>
		</dl>
		<dl class="personal-info">
			<dt>Гражданство:</dt>
			<dd><?=$arUser['UF_CITIZEN']?></dd>
			<dt>Паспорт:</dt>
			<dd><?=$arUser['UF_PASSPORT']?></dd>
			<dt>Место выдачи:</dt>
			<dd><?=$arUser['UF_PASS_PLACE']?></dd>
			<dt>Кем выдан:</dt>
			<dd><?=$arUser['UF_PASS_WHO']?></dd>
			<dt>Дата выдачи:</dt>
			<dd><?=$arUser['UF_DATE_GET']?></dd>
		</dl>
		<dl class="personal-info">
			<dt>Страна:</dt>
			<dd><?=$arUser['UF_REG_COUNTRY']?></dd>
			<dt>Субъект федерации:</dt>
			<dd><?=$arUser['UF_REG_SUBJECT']?></dd>
			<dt>Город, н/п:</dt>
			<dd><?=$arUser['UF_REG_CITY']?></dd>
			<dt>Улица:</dt>
			<dd><?=$arUser['UF_REG_STREET']?></dd>
			<dt>Дом:</dt>
			<dd><?=$arUser['UF_REG_HOME']?></dd>
			<dt>Корпус:</dt>
			<dd><?=$arUser['UF_REG_CORPUS']?></dd>
			<dt>Квартира:</dt>
			<dd><?=$arUser['UF_REG_FLAT']?></dd>
		</dl>
		<dl class="personal-info">
			<dt>Вид транспорта</dt>
			<dd><?=$arUser['UF_TRANSPORT']?></dd>
			<dt>Дата прибытия</dt>
			<dd><?=$arUser['UF_DATE_IN']?></dd>
			<dt>Город прибытия</dt>
			<dd><?=$arUser['UF_CITY_IN']?></dd>
			<dt>Номер рейса</dt>
			<dd><?=$arUser['UF_REYS']?></dd>
			<dt>Время прилета</dt>
			<dd><?=$arUser['UF_TIME_IN']?></dd>
			<dt>Дата отправления</dt>
			<dd><?=$arUser['UF_DATE_OUT']?></dd>
			<dt>Город отправления</dt>
			<dd><?=$arUser['UF_CITY_OUT']?></dd>
			<dt>Номер рейса</dt>
			<dd><?=$arUser['UF_REYS2']?></dd>
			<dt>Время вылета</dt>
			<dd><?=$arUser['UF_TIME_OUT']?></dd>
		</dl>
	</div>
</div>
<?endif?>
<?}?>