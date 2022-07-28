<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/jquery.fancybox.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/jcf.css');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.fancybox.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/fileuploader.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jcf.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.maskedinput.min.js');