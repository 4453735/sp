<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
	<title><?$APPLICATION->ShowTitle()?></title>
	<?
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/reset.css');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/style.css');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/jquery.fancybox.css');
	
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-1.11.1.min.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.fancybox.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.facedetection/jquery.facedetection.js');
	// $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/countdown.min.js');
	// $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/script.js');
	
	$APPLICATION->ShowHead();
	?>
	<!--[if lt IE 9]>
	  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!--[if lt IE 10]>
	  <script src="<?=SITE_TEMPLATE_PATH?>/js/flexibility.js"></script>
	<![endif]-->
</head>
<body>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter36670270 = new Ya.Metrika({
                    id:36670270,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/36670270" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
	<div class="page">
		<div class="page__top-stripe">
			
		</div>
		<div class="page__wrapper">
			<header class="header">
				<div class="header__line">
<!--					<div class="header__logo">-->
<!--						--><?//$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
//							"AREA_FILE_SHOW" => "file",
//							"PATH" => SITE_TEMPLATE_PATH."/include/header_logo.php",
//							"EDIT_TEMPLATE" => ""
//							),
//							false
//						);?>
<!--					</div>-->
					<div class="header__blazons">
						<?$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							"partner_site",
							Array(
								"DISPLAY_DATE" => "Y",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "Y",
								"DISPLAY_PREVIEW_TEXT" => "Y",
								"AJAX_MODE" => "N",
								"IBLOCK_TYPE" => "SportForumRussia_Partners",
								"IBLOCK_ID" => "14",
								"NEWS_COUNT" => "4",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_ORDER1" => "DESC",
								"SORT_BY2" => "SORT",
								"SORT_ORDER2" => "ASC",
								"FILTER_NAME" => "",
								"FIELD_CODE" => array(),
								"PROPERTY_CODE" => array("LINK"),
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"PREVIEW_TRUNCATE_LEN" => "",
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
								"SET_STATUS_404" => "N",
								"SET_TITLE" => "Y",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
								"ADD_SECTIONS_CHAIN" => "Y",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"INCLUDE_SUBSECTIONS" => "Y",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_FILTER" => "N",
								"CACHE_GROUPS" => "Y",
								"PAGER_TEMPLATE" => ".default",
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "Y",
								"PAGER_TITLE" => "Новости",
								"PAGER_SHOW_ALWAYS" => "Y",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "Y",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N"
							)
						);?> 
					</div>
				</div>
<!--				<div class="header__line header__line_bottom">-->
<!--					<div class="header__place-and-date">-->
<!--						--><?//$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
//							"AREA_FILE_SHOW" => "file",
//							"PATH" => SITE_TEMPLATE_PATH."/include/place_and_date.php",
//							"EDIT_TEMPLATE" => ""
//							),
//							false
//						);?>
<!--					</div>-->
<!--					<div class="header__slogan">-->
<!--						--><?//$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
//							"AREA_FILE_SHOW" => "file",
//							"PATH" => SITE_TEMPLATE_PATH."/include/slogan.php",
//							"EDIT_TEMPLATE" => ""
//							),
//							false
//						);?>
<!--					</div>-->
<!--				</div>-->
			</header>
			<section class="main">
				<div class="content">
				<?if ($APPLICATION->GetCurUri() != '/') {
					?><h1 class="sticky-header sticky-header_blue"><?$APPLICATION->ShowTitle('title', false)?></h1><?
				}?>