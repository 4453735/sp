<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
</div>
			</section>
			
			<div class="footer__background-block">
			</div>
		</div>
	</div>
	<footer class="footer">
		<div class="footer__content">
			<div class="footer__line footer__line_middle-align">
				<div class="footer__logo">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_TEMPLATE_PATH."/include/footer_logo.php",
						"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="footer__contacts">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_TEMPLATE_PATH."/include/footer_contacts.php",
						"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="footer__place-and-date">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_TEMPLATE_PATH."/include/place_and_date.php",
						"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
			<div class="footer__line footer__line_middle-align">
				<div class="footer__feedback">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_TEMPLATE_PATH."/include/footer_feedback.php",
						"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
		</div>
	</footer>
</body>
</html>