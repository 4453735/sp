<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $USER;
$userId = (int) $USER->GetID();
$arResult["VOTED_RESULTS"] = array();

if ($userId > 0) {
	$arVoteResults = CIBlockElement::GetList(array('ID' => 'ASC'), array('IBLOCK_ID' => '41', 'PROPERTY_user' => $userId), false, false, array('ID', 'NAME', 'PROPERTY_user', 'PROPERTY_vote_option'));
	while ($arVoteResult = $arVoteResults->GetNext()) {
		$arResult["VOTED_RESULTS"][$arVoteResult['ID']] = $arVoteResult['PROPERTY_VOTE_OPTION_VALUE'];
	}

	if (array_key_exists('form_filled', $_POST) && $_POST['form_filled'] == 'Y') {
		if (!array_key_exists('vote_option', $_POST) || !is_array($_POST['vote_option'])) {
			$_POST['vote_option'] = array();
		}
		foreach ($arResult["ITEMS"] as $arItem) {
			if (in_array($arItem['ID'], $_POST['vote_option']) && in_array($arItem['ID'], $arResult["VOTED_RESULTS"])) {
				// если за этот вариант проголосовали ранее и сейчас, то ничего не делаем
				continue;
			}

			if (!in_array($arItem['ID'], $_POST['vote_option']) && !in_array($arItem['ID'], $arResult["VOTED_RESULTS"])) {
				// если за этот вариант не проголосовали ни ранее, ни сейчас, то ничего не делаем
				continue;
			}

			if (!in_array($arItem['ID'], $_POST['vote_option']) && in_array($arItem['ID'], $arResult["VOTED_RESULTS"])) {
				// если за этот вариант проголосовали ранее, но не проголосовали сейчас, то удаляем результат
				$deletionVoteResultId = array_search($arItem['ID'], $arResult["VOTED_RESULTS"]);
				CIBlockElement::Delete($deletionVoteResultId);
				unset($arResult["VOTED_RESULTS"][$deletionVoteResultId]);
			}

			if (in_array($arItem['ID'], $_POST['vote_option']) && !in_array($arItem['ID'], $arResult["VOTED_RESULTS"])) {
				// если за этот вариант не проголосовали ранее, но проголосовали сейчас, то добавляем результат
				$el = new CIBlockElement;
				$PROP = array();
				$PROP[71] = $userId;
				$PROP[72] = $arItem['ID'];

				$arLoadProductArray = Array(
					"MODIFIED_BY"    => $userId, // элемент изменен текущим пользователем
					"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
					"IBLOCK_ID"      => 41,
					"PROPERTY_VALUES"=> $PROP,
					"NAME"           => $USER->GetLogin(),
					"ACTIVE"         => "Y", // активен
				);

				if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
					$arResult["VOTED_RESULTS"][] = $arItem['ID'];
				}
			}

			// для измененного числа голосов обновляем результаты:
			$votesCount = 0;
			$arVotesResults = CIBlockElement::GetList(array('ID' => 'ASC'), array('IBLOCK_ID' => '41', 'PROPERTY_vote_option' => $arItem['ID']), false, false, array('ID', 'NAME', 'PROPERTY_user', 'PROPERTY_vote_option'));
			while ($arVotesResult = $arVotesResults->GetNext()) {
				$votesCount++;
			}
			CIBlockElement::SetPropertyValuesEx($arItem['ID'], 40, array('73' => $votesCount));
		}
	}
}

$arResult["DATES"] = array();

foreach($arResult["ITEMS"] as &$arItem) {
	$activeFromUnix = MakeTimeStamp($arItem['ACTIVE_FROM'], CSite::GetDateFormat());
	$activeToUnix = MakeTimeStamp($arItem['ACTIVE_TO'], CSite::GetDateFormat());
	$onlyDateUnix = MakeTimeStamp(FormatDate('d.m.Y 00:00:00', $activeFromUnix), 'DD.MM.YYYY HH:MI:SS');
	
	$formattedDate = ToLower(FormatDate('j F Y', $activeFromUnix)) . ' года';
	$formattedTimeFrom = FormatDate('H:i', $activeFromUnix);
	$formattedTimeTo = FormatDate('H:i', $activeToUnix);
	
	$arItem["FORMATTED_DATE"] = $formattedDate;
	$arItem["FORMATTED_TIME_FROM"] = $formattedTimeFrom;
	$arItem["FORMATTED_TIME_TO"] = $formattedTimeTo;
	
	if(!isset($arResult["DATES"]["date" . $onlyDateUnix])) {
		$arResult["DATES"]["date" . $onlyDateUnix] = $formattedDate;
	}
}
?>