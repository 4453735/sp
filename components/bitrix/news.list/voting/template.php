<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (count($arResult["ITEMS"]) > 0):?>
<form method="post">
<input type="hidden" name="form_filled" value="Y">
<div class="program">
	<?
	$lastDate = null;
	$lastTimeFrom = null;
	$lastTimeTo = null;
	foreach($arResult["ITEMS"] as $arItem):
		if (is_null($lastDate) || $lastDate != $arItem['FORMATTED_DATE']) {
			?>
			<div class="program__date">
				<?=$arItem['FORMATTED_DATE']?>
			</div>
			<?
			$lastDate = $arItem['FORMATTED_DATE'];
			$lastTimeFrom = null;
			$lastTimeTo = null;
		}
		if (is_null($lastTimeFrom) || is_null($lastTimeTo) || ($lastTimeFrom != $arItem["FORMATTED_TIME_FROM"] && $lastTimeTo != $arItem["FORMATTED_TIME_TO"])) {
			?>
			<div class="program__time">
				<?=$arItem["FORMATTED_TIME_FROM"]?>&nbsp;&mdash; <?=$arItem["FORMATTED_TIME_TO"]?>
			</div>
			<?
			$lastTimeFrom = $arItem["FORMATTED_TIME_FROM"];
			$lastTimeTo = $arItem["FORMATTED_TIME_TO"];
		}
		$elementDisabledStatus = '';
		if (in_array($arItem['ID'], $arResult["VOTED_RESULTS"])) {
			$elementDisabledStatus = 'checked';
		}
		?>
		<div class="program__item">
			<input id="option-checkbox__id<?=$arItem['ID']?>" type="checkbox" name="vote_option[]" value="<?=$arItem['ID']?>" <?=$elementDisabledStatus?>>
			<label class="option-checkbox__label" for="option-checkbox__id<?=$arItem['ID']?>">
			<?if (trim($arItem['PREVIEW_TEXT']) != '') {
				echo $arItem['PREVIEW_TEXT'];
			} else {
				echo $arItem['NAME'];
			}
			?>
			</label>
		</div>
	<?endforeach;?>
	<div class="program__bottom">
		<input class="submit" type="submit" name="submit">
	</div>
</div>
</form>
<?endif;?>