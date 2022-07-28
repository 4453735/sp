<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<form name="form_auth" class="login-form" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<fieldset>
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if (strlen($arResult["BACKURL"]) > 0):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>
		
		<div class="row">
			<ul class="tabset">
<!--					<li><span style="font-size:20px"><strong><a href="http://sportdergava.ru/register/">Регистрация нового участника</a></strong></span></li>-->
<!---->
<!--				<br><p>&nbsp;</p>-->
				<li><b>Войти в личный кабинет</b></li>
			</ul>
		</div>
		<div class="row">
			<?
			ShowMessage($arParams["~AUTH_RESULT"]);
			ShowMessage($arResult['ERROR_MESSAGE']);
			//pr($arResult);
			?>
		</div>
		<div class="row">
			<label for="id1">Электронная почта:</label>
			<input name="USER_LOGIN" type="text" value="<?=$arResult["LAST_LOGIN"]?>" class="text" id="id1">
		</div>
		<div class="row">
			<label for="id2">Пароль:</label>
			<input name="USER_PASSWORD" type="password" class="text" value="" id="id2">
		</div>
		<div class="row btns-holder">
			<input type="submit" name="Login" class="submit" value="Войти" />
			<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" class="forgot">Забыли пароль?</a>
		</div>
	</fieldset>
</form>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/include/smi.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>