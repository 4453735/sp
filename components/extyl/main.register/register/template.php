<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
	
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

if($arResult['VALUES']["USER_ID"]){
	$_SESSION['NEW_REG']=$arResult['VALUES']["USER_ID"];
	header('Location: /');
}

global $USER;
CModule::IncludeModule("iblock");
$COUNTRY_ARRAY = array();
$SUBJECT_ARRAY = array();
$CATEGORY1 = array();
$TRANSPORT = array();

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$CATEGORY1[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockElement::GetList(array('NAME'=>'ASC'),array("IBLOCK_ID" => 2, 'ACTIVE' => 'Y'), false, false, array('ID','NAME',"PROPERTY_country_id"));
while($ar_res = $res -> GetNext()){
	//$COUNTRY_ARRAY[$ar_res['PROPERTY_COUNTRY_ID_VALUE']] = $ar_res['NAME'];
	$COUNTRY_ARRAY[$ar_res['ID']] = $ar_res['NAME'];
}


$res = CIBlockElement::GetList(array("NAME" => "ASC"),array("IBLOCK_ID" => 3), false, false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$SUBJECT_ARRAY[] = $ar_res['NAME'];
}
//pr($SUBJECT_ARRAY);

$res = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 5), false, false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$TRANSPORT[] = $ar_res['NAME'];
}


if (count($arResult["ERRORS"]) > 0){
		foreach ($arResult["ERRORS"] as $key => $error)
			if (intval($key) == 0 && $key !== 0) 
				$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

		ShowError(implode("<br />", $arResult["ERRORS"]));
	}
?>
<?//if($USER->isAdmin())pr($arResult);?>
<?//if($USER->isAdmin())pr($_POST);?>

<!--blah-blah-->
<!--<?print_r($_POST);?>-->
<!--blah-blah-->
<!--<?print_r($_GET);?>-->


<script>   
	var x1, y1, x2, y2, crop = '/upload/crop/';
	var jcrop_api; 
	function release(){
		jcrop_api.release();
		$('#crop').hide();
	}
	// Обрезка изображение и вывод результата
	jQuery(function($){
		$('#crop').click(function(e) {
			var img = $('#target').attr('src');
			$.post('/ajax/crop.php', {'x1': x1, 'x2': x2, 'y1': y1, 'y2': y2, 'img': img, 'crop': crop}, function(file) {
				var src = "/upload/crop/" + file;
				$('.jcrop-holder img').attr('src' , src);
				$('#target').attr('src' , src);
				$('#target').removeAttr("width");
				$('#target').removeAttr("height");
				$('#target').attr("style",'');
				$('.fancybox-inner').attr("style",'overflow: visible; width: 700px');

				$('#upload_photo').html('<img src="' + src + '" width="85" height="85" />');
				//$('#upload_photo').append('<input type="hidden"  name="file" value="' + src + '" />');
				$('[name=file]').val(src);
				$('#upload_photo').css('opacity','1.0');
				release();
				$('#upload_photo').css('opacity','1.0');
				jcrop_api.destroy();
			});
			
		});   
	});

	function createUploader(){            
		var uploader = new qq.FileUploader({
			element: document.getElementById('upload_photo'),
			debug: true,
			uploadButtonText: '<div style="width:100px;height:100px"></div>',
			onSubmit: function(){
				$('.photo').prepend("<p>&nbsp;&nbsp;Идёт загрузка</p>");
				console.log();
			},
			onComplete: function(id, fileName, responseJSON){
				$('#upload_photo').html('<img src="' + '<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName'] + '" width="85" height="85" />');
				//$('#upload_photo').append('<input type="hidden"  name="file" value="' + '<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName'] + '" />');
				$('[name=file]').val('<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName']);
				$('#upload_photo').css('opacity','1.0');
            },
			action: '/ajax/upload.php'
		});           
	}
	window.onload = createUploader;
</script>
<?if( strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')===false && strpos($_SERVER['HTTP_USER_AGENT'],'rv:11.0')===false):?>
<script type="text/javascript">
	$(function(){
		// Снять выделение	
		$('#release').click(function(e) {		
			release();
		});   
	   // Установка минимальной/максимальной ширины и высоты
	   $('#size_lock').change(function(e) {
			jcrop_api.setOptions(this.checked? {
				minSize: [ 80, 80 ],
				maxSize: [ 640, 480 ]
			}: {
				minSize: [ 0, 0 ],
				maxSize: [ 0, 0 ]
			});
			jcrop_api.focus();
		});
		
		$('#crop-start').click(function(e){
			e.preventDefault();	
			$(this).hide();
			$('#snap-else').show();
			$('#crop').show();
			function showCoords(c){
				x1 = c.x; $('#x1').val(c.x);		
				y1 = c.y; $('#y1').val(c.y);		
				x2 = c.x2; $('#x2').val(c.x2);		
				y2 = c.y2; $('#y2').val(c.y2);
				
				$('#w').val(c.w);
				$('#h').val(c.h);
				
				if(c.w > 0 && c.h > 0){
					$('#crop').show();
				}else{
					$('#crop').hide();
				}
			}
			// Получение и отправка изображения
			$('#target').Jcrop({	
				onChange:   showCoords,
				onSelect:   showCoords
			},function(){	
				jcrop_api = this;		
			});
		});

		$('#snap').click(function(e) {
			e.preventDefault();	
			$(this).hide();
			$.fancybox.showLoading();
			$('#photo').css('opacity','0.8');
			context.drawImage(photo, 0, 0, 640, 480);
			$.post('/upload_img.php', { img : canvas.toDataURL('image/jpeg') } , function(data){
				$('#target').attr('src', data);
				$('#upload_photo').html('<img src="' + data + '" width="85" height="85" />');
				$('[name=file]').val(data);
				$('#popup_camera video').hide();
				$('#target').show();
				$('#crop-start').show();
				$('#snap-else').show();
				$('#snap-save').show();
				//$('#popup_camera canvas').show();
				$.fancybox.hideLoading()
				$('#photo').css('opacity','1.0');
			});
		});
		var canvas = $("#canvas")[0],
		context = canvas.getContext("2d"),
		photo = $("#photo")[0],
		videoObj = { "video": true },
		errBack = function(error) {
			console.log("Ошибка видео захвата: ", error.code);
		};
		$('#btn-start-camera').click(function(e){
			e.preventDefault();
			$('.crop-btn').hide();
			$('#snap-else').hide();
			$('#snap').show();
			$('.jcrop-holder').remove();
			$('#target').attr('style',"display:none");
			$('#show_popup_camera').click();
			$('#popup_camera video').show();
			$('#popup_camera canvas').hide();
			navigator.getUserMedia = ( navigator.getUserMedia ||
			   navigator.webkitGetUserMedia ||
			   navigator.mozGetUserMedia ||
			navigator.msGetUserMedia);

			if(navigator.webkitGetUserMedia) {
				navigator.webkitGetUserMedia(videoObj, function(stream){
					photo.src = window.webkitURL.createObjectURL(stream);
					photo.play();
				}, errBack);
			}
			else if(navigator.msGetUserMedia) {
				navigator.msGetUserMedia(videoObj, function(stream){
					photo.src = window.URL.createObjectURL(stream);
					photo.play();
				}, errBack);
			}
			else if(navigator.mozGetUserMedia) {
				navigator.mozGetUserMedia(videoObj, function(stream){
					photo.src = window.URL.createObjectURL(stream);
					photo.play();
				}, errBack);
			}
			else if(navigator.getUserMedia) {
				navigator.getUserMedia(videoObj, function(stream){
					photo.src = window.URL.createObjectURL(stream);
					photo.play();
				}, errBack);
			}else {
			   console.log("getUserMedia not supported");
			}
		});

		$('#snap-else').click(function(e){
			e.preventDefault();
			//if(jcrop_api)jcrop_api.destroy();
			$('.crop-btn').hide();
			$('#target').attr('style',"display:none");
			$('#target').attr('width',"640");
			$('#target').attr('height',"480");
			$('#target').attr('src',"#");
			$('.jcrop-holder').remove();
			$('#popup_camera video').show();
			//$('#popup_camera canvas').hide();
			$('#snap').show();
			$(this).hide();
		});
		$('#snap-save').click(function(e){
			e.preventDefault();
			$('.fancybox-close').click();
			$('#upload_photo').css('opacity','1.0');
		});
	});
</script>
<?endif?>

<form class="reg-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
	<?
	if($arResult["BACKURL"] <> ''):
	?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?
	endif;
	?>
	<fieldset>
		<div id="step1">
			<h2>Personal and Organization</h2>
			<div class="form-box">
				<!--<h3>Персональные данные</h3>-->
				<div class="row">
					<label for="id10"><b>E-mail*</b></label>
					<input name="REGISTER[LOGIN]" type="text" class="text" value="<?=$arResult["VALUES"]['LOGIN']?>" id="id10" err="surname@example.com" placeholder="surname@example.com" data-rqr="1">
				</div>
				<div class="row">
					<label for="id500">Prefix</label>
					<input name="UF_PREFIX" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PREFIX']?>" id="id500" err="Mr, Mrs, etc" placeholder="Mr, Mrs, etc">
				</div>
				<div class="row">
					<label for="id501"><b>First name (as to appear on badge)*</b></label>
					<input name="REGISTER[NAME]" type="text" class="text" value="<?=$arResult["VALUES"]['NAME']?>" id="id501" placeholder="John" data-rqr="1">
				</div>
				<div class="row">
					<label for="id502"><b>Last name (as to appear on badge)*</b></label>
					<input name="REGISTER[LAST_NAME]" type="text" class="text" value="<?=$arResult["VALUES"]['LAST_NAME']?>" id="id502" placeholder="Smith" data-rqr="1">
				</div>
				
				<div class="divider"></div>
				<div class="row">
					<label for="id6"><b>Job title*</b></label>
					<input type="text" name="REGISTER[WORK_POSITION]" class="text" value="<?=$arResult["VALUES"]['WORK_POSITION']?>" id="id6" data-rqr="1">
				</div>
				
				<?if(empty($_REQUEST['new'])):?>
				<div class="row">
					<label for="id5"><b>Organisation name*</b></label>
					<input name="REGISTER[WORK_COMPANY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COMPANY']?>" id="id5" />
					<input name="UF_ORG_ID" type="hidden" class="text" value="" data-rqr="1" />
				</div>
				<div class="row">
					<label for="id503">Organisation country</label>
					<input name="REGISTER[WORK_COUNTRY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COUNTRY']?>" id="id503" />
				</div>
				<div class="row">
					<label for="id530">Organisation city</label>
					<input name="REGISTER[WORK_CITY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_CITY']?>" id="id530" />
				</div>
				<div class="row">
					<label for="id504">Organisation address</label>
					<input name="REGISTER[WORK_STREET]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_STREET']?>" id="id504" />
				</div>
				<div class="row">
					<label for="id505">US State/Canadian Province</label>
					<input name="REGISTER[WORK_STATE]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_STATE']?>" id="id505" />
				</div>
				<div class="row">
					<label for="id506">Province/state/canton</label>
					<input name="REGISTER[WORK_NOTES]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_NOTES']?>" id="id506" />
				</div>
				<div class="row">
					<label for="id507">Zip/postal code</label>
					<input name="REGISTER[WORK_ZIP]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_ZIP']?>" id="id507" />
				</div>
				<div class="row">
					<label for="id508"><b>Organisation phone*</b></label>
					<input name="REGISTER[WORK_PHONE]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_PHONE']?>" id="id508" data-rqr="1" />
				</div>
				<!--<div class="row">
					<label for="id555">Организация на латинском</label>
					<input name="UF_COMPANY_EN" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COMPANY']?>" id="id555" />
				</div>-->
				<?elseif($USER->IsAuthorized()):
					global $USER;
					$arParams["SELECT"] = array("UF_COMPANY_EN","UF_ORG_ID");
					$rsUser = CUser::GetList(($by="name"), ($order="desc"), array('ID' => $USER->GetID()), $arParams);
					if($arUser = $rsUser -> GetNext()){?>
						<input name="REGISTER[WORK_COMPANY]" type="hidden" class="text" value="<?=$arUser['WORK_COMPANY']?>" />
						<input name="UF_COMPANY_EN" type="hidden" class="text" value="<?=$arUser['UF_COMPANY_EN']?>" />
						<input name="UF_ORG_ID" type="hidden" class="text" value="<?=$arUser['UF_ORG_ID']?>" />
					<?}?>
				<?endif;?>
				<div class="row">
					<label for="id11">Mobile phone</label>
					<input name='tmp_mobile' type="text" class="text text-small" value="<?=$arResult["VALUES"]['PERSONAL_MOBILE']?>" id="id11" err="+41 79 679 3425" placeholder="+41 79 679 3425">
					<input id="PERSONAL_MOBILE" name='REGISTER[PERSONAL_MOBILE]' type="hidden" class="text text-small" value="" />
				</div>
				<div class="row">
					<label><b>Gender*</b></label>
					<ul class="radio-list">
						<li><input type="radio" value="M" class="radio" name="REGISTER[PERSONAL_GENDER]" id="id7"<?if($arResult["VALUES"]['PERSONAL_GENDER'] == "M" || empty($arResult["VALUES"]['PERSONAL_GENDER']))echo " checked='checked'";?>><label for="id7">male</label></li>
						<li><input type="radio" value="F" class="radio" name="REGISTER[PERSONAL_GENDER]" id="id8"<?if($arResult["VALUES"]['PERSONAL_GENDER'] == "F")echo " checked='checked'";?>><label for="id8">female</label></li>
					</ul>
				</div>
				<div class="row">
					<label><b>Upload a photo (for your accreditation)*</b></label>
					<div class="text-box">
						<div class="download-wrap">
							<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
								<div id="upload_photo" class="photo"></div>
							<?endif?>
							<!--<p>Цветная фотография на однородном фоне в формате JPG, размер фотографии не меньше чем 300х400 пикселей, фотография анфас, без головного убора, изображение лица должно занимать не менее 70% фотографии, размер не более 3 мб</p>-->
						</div>

						<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
							<input type="hidden" name="file" value="" />
							<button id="btn-start-camera" class="btn-photo">Create photo by webcam</button>
						<?else:?>
							<input type="file" name="REGISTER_FILES_PERSONAL_PHOTO" />
						<?endif?>
					</div>
				</div>
				<div class="divider"></div>
				
				<div class="row">
					<p>Completing This Form on Behalf of Another Person.</p>
					<p>If you are completing this form on behalf of another person, enter your name, phone and email address so that we may contact you if necessary.</p>
				</div>
				<div class="row">
					<label for="id509">Name</label>
					<input name="UF_ANOTHER_NAME" type="text" class="text" value="<?=$arResult["VALUES"]['UF_ANOTHER_NAME']?>" id="id509">
				</div>
				<div class="row">
					<label for="id510">Telephone</label>
					<input name="UF_ANOTHER_PHONE" type="text" class="text" value="<?=$arResult["VALUES"]['UF_ANOTHER_PHONE']?>" id="id510">
				</div>
				<div class="row">
					<label for="id511">E-mail</label>
					<input name="UF_ANOTHER_EMAIL" type="text" class="text" value="<?=$arResult["VALUES"]['UF_ANOTHER_EMAIL']?>" id="id511">
				</div>
				
				<!--
				<div class="row">
					<label for="id3">ФИО</label>
					<input type="text" name="UF_FIO" class="text" value="<?=$arResult["VALUES"]['UF_FIO']?>" err="Иванов Иван Иванович" placeholder = "Иванов Иван Иванович" id="id3">
				</div>
				<div class="row">
					<label for="id5">Дата рождения</label>
					<select name="d">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m'>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y'>
						<?for($i=date('Y');$i>1930;$i--):?>
							<option value="<?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<input id="PERSONAL_BIRTHDAY" type="hidden" name="REGISTER[PERSONAL_BIRTHDAY]" />
				</div>
				<div class="row">
					<label>Гражданство</label>
					<select  name="UF_CITIZEN" class="select-large">
						<?foreach($COUNTRY_ARRAY as $k => $v):?>
							<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="divider"></div>
				<h3>Место рождения</h3>
				<div class="row">
					<label>Страна</label>
					<select name="UF_BIRTH_COUNTRY" class="select-large">
						<?foreach($COUNTRY_ARRAY as $k => $v):?>
							<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="row">
					<label>Субъект федерации</label>
					<select name="UF_BIRTH_SUBJECT" class="select-large">
						<option value="Москва">Москва</option>
						<option value="Санкт-Петербург">Санкт-Петербург</option>
						<?foreach($SUBJECT_ARRAY as $k => $v):?>
							<option value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div id="UF_BIRTH_COUNTRY" class="row">
					<label for="id101">Город/населенный пункт</label>
					<input name="UF_BIRTH_CITY" type="text" class="text" value="<?=$arResult["VALUES"]['UF_BIRTH_CITY']?>" placeholder="Москва" id="id101">
				</div><br/>
				<div class="divider"></div>
				
				<div class="row">
					<div class="check-holder">
						<input type="checkbox" name="UF_UPOLN" value="да" class="checkbox" id="id9" />
						<label for="id9">Я — </label>
						<span style="color:#576570">уполномоченное доверенное лицо делегации</span>
					</div>
				</div>
				<div class="divider"></div>
				<div class="deligation-note" style="font-size:12px;color:#576570">
					<span>Уполномоченное доверенное лицо делегации:</span>
					<p style="font-size:12px;color:#576570">— может регистрировать всех делегатов в личном кабинете и вносить изменения;<br/>— может получить бейдж за делегацию по доверенности (образец доверенности вы можете скачать в личном кабинете)</p>
				</div>
				<div class="divider"></div>
				<h3>Контактная информация</h3>
				<div class="row">
					<div class="check-holder">
						<input name="UF_SMS_AGREE" value="4" type="checkbox" class="checkbox" id="id12">
						<label for="id12">Согласен на получение СМС-уведомлений от Организатора</label>
					</div>
				</div>
				<div class="row">
					<div class="check-holder">
						<input type="checkbox" class="checkbox" id="id13">
						<label for="id13">Согласен на обработку своих персональных данных</label><br><br>
						<div style="border: 1px solid #bbbfbf; height: 110px; overflow: auto; padding: 5px;">
							В соответствии со статьей 9 Федерального закона от 27 июля 2006 года N 152-ФЗ "О персональных данных" даю своё согласие АНО «Форум «Спортивная Держава» (далее – Организатор) на обработку моих персональных данных Организатором в целях обеспечения безопасности для участия в Международном спортивном форуме «Россия – спортивная держава» (далее – Форум). <br><br>
							Я согласен предоставить информацию, относящуюся к моей личности: фамилия, имя, отчество, дата рождения, контактный телефон, адрес e-mail, паспортные данные и место регистрации уполномоченным специализированным учреждениям безопасности РФ на период подготовки и проведения Форума и подтверждаю, что давая такое согласие, я действую своей волей и в своем интересе. <br><br>
							Я уведомлен и понимаю, что под обработкой персональных данных подразумевается сбор, систематизация, накопление, хранение, уточнение (обновление, изменение), использование, распространение, уничтожение и любые другие действия в соответствии с действующим законодательством. Обработка данных может осуществляться с использованием средств автоматизации, так и без их использования (при неавтоматической обработке).
						</div>
					</div>
				</div>-->
				<div class="submit-wrap">
					<input type="submit" class="submit go-step2" value="Continue" />
					<span class="btn-note"></span>
				</div>
			</div>
		</div>
		<div id="step2" style="display:none">
			<div class="form-box">
				<h2>Visa and Entry to Russian Federation</h2>
				<div class="row">
					<p>An expedited Russian visa process is put in place by the Russian Authorities. </p>
					<p>Please do note that the Russian Authorities will not initiate any visa application before 2 February 2015.</p>
					<p>Please indicate below your nationality and if you require an entry visa for the Russian Federation. After having answered the visa questions, the Local Organising Committee (LOC) will provide you with an official invitation letter after 16 February 2015.</p>
					<p>Please note that you can ONLY file your visa application with the Russian Diplomatic Mission once you have received the official invitation letter from the LOC after 16 February 2015.</p>
					<p>For Russian visa information for <span style="color:#f00;">наименование мероприятия</span>, please click here. </p>
					<p>*&nbsp;&mdash; Please note that it is the delegate’s responsibility to obtain an entry visa for the Russian Federation. If for whatever reason the visa is not granted, the cancellation terms and conditions for SportAccord Convention 2015 will still apply regardless of the visa outcome.</p>
				</div>
				<div class="row">
					<label><b>Your Nationality*</b></label>
					<select name="UF_CITIZEN" class="select-large">
						<?foreach($COUNTRY_ARRAY as $k => $v):?>
							<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="row">
					<label><b>Do you need a visa to enter the Russian Federation?*</b></label>
					<ul class="radio-list">
						<li><input type="radio" value="28" class="radio" name="UF_NEED_VISA" id="id512"<?if($arResult["VALUES"]['UF_NEED_VISA'] == 28 || empty($arResult["VALUES"]['PERSONAL_GENDER']))echo " checked='checked'";?>><label for="id512">yes</label></li>
						<li><input type="radio" value="29" class="radio" name="UF_NEED_VISA" id="id513"<?if($arResult["VALUES"]['UF_NEED_VISA'] == 29)echo " checked='checked'";?>><label for="id513">no</label></li>
					</ul>
				</div>
				<div class="row">
					<label for="id4"><b>Full name as in passport*</b></label>
					<input type="text" name="UF_FIO_EN" class="text" value="<?=$arResult["VALUES"]['UF_FIO_EN']?>" id="id4" data-rqr="1">
				</div>
				<div class="row">
					<label for="id20"><b>Passport number*</b></label>
					<input name="UF_PASSPORT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PASSPORT']?>" id="id20" data-rqr="1">
				</div>
				<div class="row">
					<label>Please upload a <b>digital Black & White</b> scan of the page in your passport with passport details. (The following formats can be uploaded: jpg, png, doc and pdf. with file names in alphanumeric characters only)*</label>
					<div class="text-box">
						<div class="download-wrap">
							<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
								<div id="upload_photo2" class="photo"></div>
							<?endif?>
							<!--<p>Цветная фотография на однородном фоне в формате JPG, размер фотографии не меньше чем 300х400 пикселей, фотография анфас, без головного убора, изображение лица должно занимать не менее 70% фотографии, размер не более 3 мб</p>-->
						</div>
						<input type="file" name="UF_PASSPORT_SCAN" />
					</div>
				</div>
				<div class="row">
					<label for="id514"><b>Expiry date of passport*</b></label>
					<select name="d">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m'>
						<?for($i=1;$i<13;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=date("F",  mktime(0, 0, 0, $i, 1, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y'>
						<?for($i=date('Y');$i<((int)date('Y')+21);$i++):?>
							<option value="<?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<input id="UF_PASSPORT_EXPIRY" type="hidden" name="UF_PASSPORT_EXPIRY" />
				</div>
				<div class="row">
					<div class="check-holder">
						<input name="UF_USE_PHOTO" value="30" type="checkbox" class="checkbox" id="id515">
						<label for="id515">Use of your photo</label>
						<br><br>
						<div style="border: 1px solid #bbbfbf; height: 57px; overflow: auto; padding: 5px;">
							Please note that your ID Photo will be used for both your accreditation badge and within The Networker site. If you do not wish to appear in the Networker, please check this box.
						</div>
					</div>
				</div>
			
			
				<!--<h3>Паспортные данные</h3>
				<div class="row">
					<label for="id20">Серия и номер паспорта</label>
					<input name="UF_PASSPORT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PASSPORT']?>" placeholder="4610 762874" err="4610 762874" id="id20">
				</div>
				<div class="row">
					<label for="id22">Кем выдан</label>
					<input name="UF_PASS_WHO" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PASS_WHO']?>" err="заполните, как указано в паспорте" placeholder="заполните, как указано в паспорте" id="id22">
				</div>
				<div class="row">
					<label for="id23">Дата выдачи</label>
					<input name="UF_DATE_GET" type="text" class="text" value="<?=$arResult["VALUES"]['UF_DATE_GET']?>" err="04.01.2004" placeholder="04.01.2004" id="id23">
				</div>
				<div class="row">
					<label for="id24">Номер аккредитации МИД РФ (для иностранных участников)</label>
					<input name="UF_NUM_ACCRED" type="text" class="text" value="" placeholder="не обязательное" id="id24">
				</div>
				<div class="divider"></div><br/>
				<h3>Место регистрации</h3>
				<div class="row">
					<label>Страна</label>
					<select name="UF_REG_COUNTRY" class="select-large">
						<?foreach($COUNTRY_ARRAY as $k => $v):?>
							<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="row">
					<label>Субъект федерации</label>
					<select name="UF_REG_SUBJECT" class="select-large">
						<option value="Москва">Москва</option>
						<option value="Санкт-Петербург">Санкт-Петербург</option>
						<?foreach($SUBJECT_ARRAY as $k => $v):?>
							<option value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="row">
					<label for="id300">Район</label>
					<input name="UF_REG_SECTOR" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_SECTOR']?>" id="id300">
				</div>
				<div id="UF_REG_COUNTRY" class="row">
					<label for="id102">Город/населенный пункт</label>
					<input name="UF_REG_CITY" type="text" class="text" value="<?=$arResult["VALUES"]['UF_BIRTH_CITY']?>" placeholder="Москва" id="id102">
				</div>
				<div class="row">
					<label for="id26">Улица</label>
					<input name="UF_REG_STREET" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_STREET']?>" err="Краснознаменская" placeholder="Краснознаменская" id="id26">
				</div>
				<div class="row">
					<label for="id27">Дом</label>
					<input name="UF_REG_HOME" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_HOME']?>" id="id27">
				</div>
				<div class="row">
					<label for="id28">Корпус/Строение</label>
					<input name="UF_REG_CORPUS" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_CORPUS']?>" id="id28">
				</div>
				<div class="row">
					<label for="id29">Квартира</label>
					<input name="UF_REG_FLAT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_FLAT']?>" id="id29">
				</div>
				
				<div class="divider"></div><br/>
				<h3>Место фактического проживания</h3>
				<div class="row">
					<div class="check-holder">
						<input type="checkbox" name="SOVPAD" value="Y" class="checkbox" id="id900" />
						<label for="id900">Совпадает с адресом регистрации</label>
					</div>
				</div>
				<span id="mesto_proj">
					<div class="row">
						<label>Страна</label>
						<select name="UF_REAL_COUNTRY" class="select-large">
							<?foreach($COUNTRY_ARRAY as $k => $v):?>
								<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>
					</div>
					<div class="row">
						<label>Субъект федерации</label>
						<select name="UF_REAL_SUBJECT" class="select-large">
							<option value="Москва">Москва</option>
							<option value="Санкт-Петербург">Санкт-Петербург</option>
							<?foreach($SUBJECT_ARRAY as $k => $v):?>
								<option value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>
					</div>
					<div class="row">
						<label for="id301">Район</label>
						<input name="UF_REAL_SECTOR" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REAL_SECTOR']?>" id="id301">
					</div>
					<div id="UF_REAL_COUNTRY" class="row">
						<label for="id100">Город/населенный пункт</label>
						<input name="UF_REAL_CITY" type="text" class="text" value="<?=$arResult["VALUES"]['UF_BIRTH_CITY']?>" placeholder="Москва" id="id100" />
						<?/*<select name="UF_REAL_CITY" class="select-large">
							<?foreach($RUS_CITY_ARRAY as $v):?>
								<option value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>*/?>
					</div>
					
					<div class="row">
						<label for="id126">Улица</label>
						<input name="UF_REAL_STREET" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_STREET']?>" err="Краснознаменская" placeholder="Краснознаменская" id="id126">
					</div>
					<div class="row">
						<label for="id127">Дом</label>
						<input name="UF_REAL_HOME" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_HOME']?>" id="id127">
					</div>
					<div class="row">
						<label for="id128">Корпус/Строение</label>
						<input name="UF_REAL_CORPUS" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_CORPUS']?>" id="id128">
					</div>
					<div class="row">
						<label for="id129">Квартира</label>
						<input name="UF_REAL_FLAT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_FLAT']?>" id="id129">
					</div>
				</span>-->
					<div class="submit-wrap">
						<input type="submit" class="submit go-step1" value="Back">
						<input type="submit" class="submit go-step3" value="Continue" />
						<span class="btn-note"></span>
					</div>
			</div>
		</div>
		<div id="step3" style="display:none">
			<h2>Accommodation and Travel</h2>
			<div class="form-box">
				<div class="row">
					<h3>Hotel Accommodation - Important Information</h3>
					<p>LOC is pleased to propose accommodation in the following Official Hotels with rooms distributed as mentioned below.</p>
					<p>Please note that hotel rooms are booked on a first-come, first-serve basis.</p>
					<p>Only if you book your hotel accommodation in one of the below mentioned Official Hotels, will you be provided with complimentary logistical support throughout the FORUM.</p>
					<h4>Official Hotels & Room Distribution</h4>
					<p>A. Radisson Blu Resort & Congress Centre 5*</p>
					<p>B.  Imeretinskiy Hotel 4*</p>
					<p>Headquarter Hotel - Accommodation provided only for International Federations* and IOC.</p>
					<h4>Hotel room rates</h4>
					<p>Hotel room rates include breakfast and Wi-Fi Internet. </p>
					<p>Extra's like mini bar, laundry service, etc. are to be paid upon check out.</p>
					<h4>Map Official Hotels</h4>
					<p>Please click here for the location of the official Hotels.</p>
				</div>
				<div class="row">
					<label><b>Accommodation*</b></label>
					<div><input type="radio" value="31" class="radio" name="UF_ACCOMMODATION" id="id516"<?if($arResult["VALUES"]['UF_ACCOMMODATION'] == 31 || empty($arResult["VALUES"]['UF_ACCOMMODATION']))echo " checked='checked'";?>><label for="id516">I need accommodation</label></div>
					<div><input type="radio" value="32" class="radio" name="UF_ACCOMMODATION" id="id517"<?if($arResult["VALUES"]['UF_ACCOMMODATION'] == 32)echo " checked='checked'";?>><label for="id517">I am sharing accommodation with another registrant</label></div>
					<div><input type="radio" value="33" class="radio" name="UF_ACCOMMODATION" id="id518"<?if($arResult["VALUES"]['UF_ACCOMMODATION'] == 33)echo " checked='checked'";?>><label for="id518">I do not require accommodation</label></div>
				</div>
				<div class="row">
					<h3>Hotel Accommodation - Terms and Conditions</h3>
					<p>To confirm your hotel accommodation for название мероприятия, all hotel room nights booked are to be paid in full upon completion of your registration.</p>
					<p>A minimum of two (2) nights must be reserved by the delegate between ------- 2015</p>
					<p>Hotel room booking(s) can only be paid by credit card. </p>
				</div>
				<div class="row">
					<div class="check-holder">
						<input name="UF_AGREE_HOTEL" value="34" type="checkbox" class="checkbox" id="id519">
						<label for="id519">I agree to the hotel room booking terms and cancellation policies*</label>
					</div>
				</div>
				<h3>Travel Information - Arrival</h3>
				<div class="row">
					<label for="id520">Airline</label>
					<input type="text" name="UF_ARRIVAL_AIRLINE" class="text" value="<?=$arResult["VALUES"]['UF_ARRIVAL_AIRLINE']?>" id="id520">
				</div>
				<div class="row">
					<label for="id521">Flight Number</label>
					<input type="text" name="UF_ARRIVAL_FLIGHT" class="text" value="<?=$arResult["VALUES"]['UF_ARRIVAL_FLIGHT']?>" id="id521">
				</div>
				<div class="row">
					<label for="id522">Arrival City</label>
					<input type="text" name="UF_ARRIVAL_CITY" class="text" value="<?=$arResult["VALUES"]['UF_ARRIVAL_CITY']?>" id="id522">
				</div>
				<div class="row">
					<label for="id5">Arrival Date</label>
					<select name="d2">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m2'>
						<?for($i=1;$i<13;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=date("F",  mktime(0, 0, 0, $i, 1, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y2'>
						<option value="<?=date('Y')?>"><?=date('Y')?></option>
					</select>
					<input id="UF_DATE_IN" type="hidden" name="UF_DATE_IN" />
				</div>
				<div class="row">
					<label for="id523">Сonnection Information</label>
					<input type="text" name="UF_ARRIVAL_INFO" class="text" value="<?=$arResult["VALUES"]['UF_ARRIVAL_INFO']?>" id="id523">
				</div>

				<h3>Travel Information - Return</h3>
				<div class="row">
					<label for="id524">Airline</label>
					<input type="text" name="UF_DEPARTURE_AIRLINE" class="text" value="<?=$arResult["VALUES"]['UF_DEPARTURE_AIRLINE']?>" id="id524">
				</div>
				<div class="row">
					<label for="id525">Flight Number</label>
					<input type="text" name="UF_DEPARTURE_FLIGHT" class="text" value="<?=$arResult["VALUES"]['UF_DEPARTURE_FLIGHT']?>" id="id525">
				</div>
				<div class="row">
					<label for="id526">Departure City</label>
					<input type="text" name="UF_DEPARTURE_CITY" class="text" value="<?=$arResult["VALUES"]['UF_DEPARTURE_CITY']?>" id="id526">
				</div>
				<div class="row">
					<label for="id5">Departure Date</label>
					<select name="d3">
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m3'>
						<?for($i=1;$i<13;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=date("F",  mktime(0, 0, 0, $i, 1, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y3'>
						<option value="<?=date('Y')?>"><?=date('Y')?></option>
					</select>
					<input id="UF_DATE_OUT" type="hidden" name="UF_DATE_OUT" />
				</div>
				<div class="row">
					<label for="id527">Сonnection Information</label>
					<input type="text" name="UF_DEPARTURE_INFO" class="text" value="<?=$arResult["VALUES"]['UF_DEPARTURE_INFO']?>" id="id527">
				</div>
				
				<h3>Miscellaneous Travel Information</h3>
				<div class="row">
					<label for="id529">Additional Information</label>
					<textarea name="UF_TRAVEL_INFO" class="text" id="id529"><?=$arResult["VALUES"]['UF_TRAVEL_INFO']?></textarea>
				</div>
				
				<h3>Travel Information - Other</h3>
				<div class="row">
					<div style="border: 1px solid #bbbfbf; height: 110px; overflow: auto; padding: 5px;">
						<h4>Complimentary Transport</h4>
						<p>Complimentary ground transportation from Moscow (National and International) Airport to any of the Official Forum Hotels will be provided by our Local Organizing Committee. Forum airport staff will meet you at the airport terminal and direct you to the appropriate transport.   In order for us to plan for adequate and timely transportation, and reduce our environmental impact, please be sure to provide your correct flight details on this registration form. If you do not have your flight details at this time you can come back and complete this information at a later stage.</p>
						<h4>Disabled Transport Requirements</h4>
						<p>If you require any additional assistance for transportation, please tick the box below.</p>
						<p>Forum will contact you for further info. </p>
					</div>
					<div class="check-holder">
						<input name="UF_DISABLED_ASSISTAN" value="35" type="checkbox" class="checkbox" id="id528">
						<label for="id528">Please tick this box if you require disabled transport assistance</label>
					</div>
				</div>
				<div class="row">
					To continue and complete this registration, click on Continue.
				</div>
				<!--<div class="row" style="text-align: center">
					<label style="display:none" id='UF_CAT1'>Категория 1</label>
					<select name="UF_CAT1" class="select-large">
						<option value="">не указана</option>
						<?foreach($CATEGORY1 as $k => $v):?>
							<option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div id = "UF_CAT2" class="row" style="display:none;text-align: center">
					<label style="display:none">Категория 2</label>
					<select name="UF_CAT2" class="select-large">
						<option value="">не указана</option>
					</select>
				</div>
				<div id = "UF_CAT3" style="display:none;text-align: center" class="row">
					<label style="display:none">Категория 3</label>
					<select name="UF_CAT3" class="select-large">
						<option value="">не указана</option>
					</select>				
				</div>
				<div class="submit-wrap">
					<input type="submit" class="submit go-step2" value="Назад" />
					<input type="submit" class="submit go-step4" value="Далее" />
					<span class="btn-note"></span>
				</div>-->
				<div class="submit-wrap">
					<input type="submit" class="submit go-step2" value="Back">
					<input id="register_submit_button" type="submit" name="register_submit_button" class="submit" value="Continue" />
					<input type="hidden" name="register_submit_button" class="submit" value="Continue" />
					<span class="btn-note"></span>
				</div>
			</div>
		</div>
		<!--<div id="step4" style="display:none">
			<h2>Регистрация шаг 4: Укажите предварительные сроки поездки</h2>
			<div class="form-box">

				<div class="row">
					<label>Предполагаемый вид транспорта</label>
					<select name="UF_TRANSPORT" class="select-large">
						<?foreach($TRANSPORT as $v):?>
							<option value="<?=$v?>"><?=$v?></option>
						<?endforeach?>
					</select>
				</div>
				<div id="idhide" style="display:none" class="row">
					<label id="UF_AUTO_NUM" for="id1270">Гос номер</label>
					<input name="UF_AUTO_NUM" type="text" class="text" value="<?=$arResult["VALUES"]['UF_AUTO_NUM']?>" id="id1270">
					<div class="deligation-note" style="font-size:12px;color:#576570">
						<span>Для получения аккредитации на автомобиль</span>
					</div>
				</div>
				<div class="submit-wrap">
					<input type="submit" class="submit go-step3" value="Назад">
					<input id="register_submit_button" type="submit" name="register_submit_button" class="submit" value="Завершить" />
					<input type="hidden" name="register_submit_button" class="submit" value="Завершить" />
					<span class="btn-note"></span>
				</div>
			</div>
		</div>-->
	</fieldset>
</form>

<a class="fancybox" id="show_popup_camera" href="#popup_camera"></a>
<div style="display: none; margin: 0;" class="reg-form" id="popup_camera">
	<div class="form-box visible">
		<canvas id="canvas" width="640" height="480"></canvas>
		<video width="640" height="480" id="photo" autoplay=""></video>
		<img src="#" style="display:none" width="640" height="480" id="target" />
		<div style="display:none" class="inline-labels">
			<label>X1 <input type="text" size="4" id="x1" name="x1" /></label>
			<label>Y1 <input type="text" size="4" id="y1" name="y1" /></label>
			<label>X2 <input type="text" size="4" id="x2" name="x2" /></label>
			<label>Y2 <input type="text" size="4" id="y2" name="y2" /></label>
			<label>W <input type="text" size="4" id="w" name="w" /></label>
			<label>H <input type="text" size="4" id="h" name="h" /></label>
		</div>
		<p id="but_snap">
			<button id="snap" class="btn-photo">Create photo by webcam</button>
			<button class="btn-photo crop-btn" id="crop">Crop</button>
			<button class="btn-photo crop-btn" id="crop-start">Edit</button>
			<button style="display:none" class="btn-photo" id="snap-else" class="btn-photo">Create new photo</button>
			<button style="float:right;display:none;" id="snap-save" class="btn-photo">Save</button>
		</p>
	</div>
</div>




<script>
	<?if( strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')!==false || strpos($_SERVER['HTTP_USER_AGENT'],'rv:11.0')!==false):?>
		$('#btn-start-camera').remove();
	<?endif?>

	// $('[name=UF_TRANSPORT]').change(function(){
		// if($('[name=UF_TRANSPORT] option:selected').val() == "Личный автомобиль"){
			// $('#idhide').show();
		// }
		// else{
			// $('#idhide').hide();
		// }
	// });
	// $('[name = tmp_mobile]').change(function(){
		// $('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val().replace("+7", "8"));
	// });
		
	// $("#id900").change(function(){
		// $(this).toggleClass('checked');
		// if($(this).hasClass('checked')){
			// $('#mesto_proj').hide();
			// $('[name=UF_REAL_STREET]').val($('[name=UF_REG_STREET]').val());
			// $('[name=UF_REAL_HOME]').val($('[name=UF_REG_HOME]').val());
			// $('[name=UF_REAL_CORPUS]').val($('[name=UF_REG_CORPUS]').val());
			// $('[name=UF_REAL_FLAT]').val($('[name=UF_REG_FLAT]').val());
			// $('[name=UF_REAL_CITY]').val($('[name=UF_REG_CITY]').val());
			// $('[name=UF_REAL_SECTOR]').val($('[name=UF_REG_SECTOR]').val());
			// $('[name=UF_REAL_COUNTRY]').val($('[name=UF_REG_COUNTRY]').val());
			// $('[name=UF_REAL_SUBJECT]').val($('[name=UF_REG_SUBJECT]').val());
		// }
		// else{
			// $('#mesto_proj').show();
		// }
	// });

	
	$(document).ready(function () {
		// $("#show_popup").click();
		//$('#id23').mask("99.99.9999");
		// $('#id11').mask("+79999999999");
		// $('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val().replace("+7", "8"));
	});
	function isValidEmail (email, strict){
		if ( !strict ) email = email.replace(/^s+|s+$/g, '');
		return (/^([a-z0-9_-]+.)*[a-z0-9_-]+@([a-z0-9][a-z0-9-]*[a-z0-9].)+[a-z]{2,4}$/i).test(email);
	}
	// $('#id13').change(function(){
		// if($('.go-step2').attr('disabled')=='disabled')$('.go-step2').attr('disabled', false);
		// else $('.go-step2').attr('disabled', 'disabled');
	// });
	$('.go-step1').click(function(e){
		e.preventDefault();
		$('#step1').show();
		$(this).parent().parent().parent().hide();
	});
	$('.go-step2').click(function(e){
		e.preventDefault();
		$('#step1 .btn-note').html('');
		var err = 0;
		var err_text = 'Please, fill next fields:<br/>';
		var passportExpiry = $('[name=d]').val() + '.' + $('[name=m]').val() + '.' + $('[name=y]').val();
		$('#UF_PASSPORT_EXPIRY').val(passportExpiry);
		$('#step1 [type=text][data-rqr=1]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				err++;
				err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find('label').text() + '</a>&nbsp;';
			}
		});
		if(!isValidEmail($('#id10').val(),true) && $('#id10').val() != ''){
			err++;
			err_text += '<br><a href="#' + $('#id10').attr('id') + '">Not correct e-mail</a>';
		}
		
		// if($('[name=UF_FIO]').val() != '' && $('[name=UF_FIO]').val() != $('[name=UF_FIO]').attr('err')){
			// var reg = /^[а-яА-Я0-9 .,]+$/;
			// if(!reg.test($('[name=UF_FIO]').val())){
				// err++;
				// err_text += '<br><a href="#id3">ФИО - Разрешены только русские буквы</a>';
			// }
		// }
		// if($('[name=UF_COMPANY_EN]').val() != '' && $('[name=UF_COMPANY_EN]').val() != $('[name=UF_COMPANY_EN]').attr('err')){
			// var reg = /^[a-zA-Z0-9 .,'";/_:-]+$/;
			// if(!reg.test($('[name=UF_COMPANY_EN]').val())){
				// err++;
				// err_text += '<br><a href="#id555">Организация на латинском - Разрешены только англ. буквы</a>';
			// }
		// }
		<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
		if($('[name=file]').val() == ""){
			err++;
			err_text += '<br><a href="#upload_photo">Upload a photo</a>';
		}
		<?endif?>
		var tmp_el = $(this);
		$.post('/ajax/get_email.php', {EMAIL: $('#id10').val()}, function(data){
			if(data=='ok'){
				err++;
				err_text += '<br><a href="#' + $('#id10').attr('id') + '">This e-mail already exists</a>';
			}
			if(err==0){
				$('#step2').show();
				tmp_el.parent().parent().parent().hide();
				$('html, body').animate({scrollTop: 0}, 700);
			}
			else{
				$('#step1 .btn-note').html(err_text);
			}
		});
	});
	$('.go-step3').click(function(e){
		e.preventDefault();
		if($('#id900').hasClass('checked')){
			$('#mesto_proj').hide();
			$('[name=UF_REAL_STREET]').val($('[name=UF_REG_STREET]').val());
			$('[name=UF_REAL_HOME]').val($('[name=UF_REG_HOME]').val());
			$('[name=UF_REAL_CORPUS]').val($('[name=UF_REG_CORPUS]').val());
			$('[name=UF_REAL_FLAT]').val($('[name=UF_REG_FLAT]').val());
			$('[name=UF_REAL_CITY]').val($('[name=UF_REG_CITY]').val());
			$('[name=UF_REAL_SECTOR]').val($('[name=UF_REG_SECTOR]').val());
			$('[name=UF_REAL_COUNTRY]').val($('[name=UF_REG_COUNTRY]').val());
			$('[name=UF_REAL_SUBJECT]').val($('[name=UF_REG_SUBJECT]').val());
		}
		
		
		$('#step2 .btn-note').html('');
		var err = 0;
		var err_text = 'Please, fill next fields:<br/>';
		$('#step2 [type=text][data-rqr=1]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				if($(this).attr('name') != "UF_NUM_ACCRED" && $(this).attr('name') != "UF_REG_SECTOR" && $(this).attr('name') != "UF_REAL_SECTOR" && $(this).attr('name') != "UF_REG_CORPUS" && $(this).attr('name') != "UF_REAL_CORPUS"){
					err++;
					err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find('label').text() + '</a>&nbsp;';
				}
			}
		});
		<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
		if($('[name=FILES_UF_PASSPORT_SCAN]').val() == ""){
			err++;
			err_text += '<br><a href="#upload_photo2">Upload a scan of the passport</a>';
		}
		<?endif?>
		if(err==0){
			$('#step3').show();
			$(this).parent().parent().parent().hide();
			$('html, body').animate({scrollTop: 0}, 700);
		}
		else{
			$('#step2 .btn-note').html(err_text);
		}
	});
	// $('.go-step4').click(function(e){
		// e.preventDefault();
		// $('#step3 .btn-note').html('');
		// var err = 0;
		// var err_text = 'Вы не заполнили поля:<br/>';
		// if($('[name=UF_CAT1]').val() == "" || $(this).val() == $(this).attr('err')){
				// err++;
				// err_text += '<a href="#UF_CAT1">Категория 1</a>';			
		// }
		// if(err==0){
			// $('#step4').show();
			// $(this).parent().parent().parent().hide();
			// $('html, body').animate({scrollTop: 0}, 700);
		// }
		// else{
			// $('#step3 .btn-note').html(err_text);
		// }
	// });
	$('#register_submit_button').click(function(e){
		e.preventDefault();
		var UF_DATE_OUT = $('[name=d3]').val() + '.' + $('[name=m3]').val() + '.' + $('[name=y3]').val();
		$('#UF_DATE_OUT').val(UF_DATE_OUT);
		var UF_DATE_IN = $('[name=d2]').val() + '.' + $('[name=m2]').val() + '.' + $('[name=y2]').val();
		$('#UF_DATE_IN').val(UF_DATE_IN);
		
		$('#step1 .btn-note').html('');
		var err = 0;
		var err_text = 'Вы не заполнили поля:<br/>';
		if($('[name=UF_TRANSPORT] option:selected').val() == "Личный автомобиль" && $('[name=UF_AUTO_NUM]').val() == ''){
			err++;
			err_text += '<a href="#UF_AUTO_NUM">Номер автомобиля</a>';	
		}
		if(err==0){
			$('.reg-form').submit();
		}
		else{
			$('#step4 .btn-note').html(err_text);
		}
	});
	
	// $('[name=UF_REAL_COUNTRY]').change(function(){
		// if($('[name=UF_REAL_COUNTRY] option:selected').attr('country') == 3159){
			// $('#UF_REAL_COUNTRY').prev().show();
		// }
		// else{
			// $('#UF_REAL_COUNTRY').prev().hide();
		// }
		// /*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_REAL_COUNTRY] option:selected').attr('country')}, function(data){
			// data = jQuery.parseJSON(data);
			// $('#UF_REAL_COUNTRY .select-large').html('');
			// for(var i=0;i<data.length;i++){
				// $('#UF_REAL_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			// }
		// });*/
	// });
	
	// $('[name=UF_BIRTH_COUNTRY]').change(function(){
		// if($('[name=UF_BIRTH_COUNTRY] option:selected').attr('country') == 3159){
			// $('#UF_BIRTH_COUNTRY').prev().show();
		// }
		// else{
			// $('#UF_BIRTH_COUNTRY').prev().hide();
		// }
		// /*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_BIRTH_COUNTRY] option:selected').attr('country')}, function(data){
			// data = jQuery.parseJSON(data);
			// $('#UF_BIRTH_COUNTRY .select-large').html('');
			// for(var i=0;i<data.length;i++){
				// $('#UF_BIRTH_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			// }
		// });*/
	// });
	
	// $('[name=UF_REG_COUNTRY]').change(function(){
		// if($('[name=UF_REG_COUNTRY] option:selected').attr('country') == 3159){
			// $('#UF_REG_COUNTRY').prev().show();
		// }
		// else{
			// $('#UF_REG_COUNTRY').prev().hide();
		// }
		// /*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_REG_COUNTRY] option:selected').attr('country')}, function(data){
			// data = jQuery.parseJSON(data);
			// $('#UF_REG_COUNTRY .select-large').html('');
			// for(var i=0;i<data.length;i++){
				// $('#UF_REG_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			// }
		// });*/
	// });
	
	
	// $('[name=UF_CAT1]').change(function(){
		// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT1] option:selected').attr('sect')}, function(data){
			// data = jQuery.parseJSON(data);
			// $('[name=UF_CAT2]').html('');
			// $('[name=UF_CAT3]').html('');
			// for(var i=0;i<data.ID.length;i++){
				// $('[name=UF_CAT2]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
				// $('#UF_CAT2').show();
			// }
			// $('#UF_CAT3').hide();
			// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2] option:selected').attr('sect')}, function(data){
				// data = jQuery.parseJSON(data);
				// $('[name=UF_CAT3]').html('');
				// for(var i=0;i<data.ID.length;i++){
					// $('[name=UF_CAT3]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
					// $('#UF_CAT3').show();
				// }
			// });
		// });
	// });
	
	// $('[name=UF_CAT2]').change(function(){
		// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2] option:selected').attr('sect')}, function(data){
			// data = jQuery.parseJSON(data);
			// $('[name=UF_CAT3]').html('');
			// for(var i=0;i<data.ID.length;i++){
				// $('[name=UF_CAT3]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
				// $('#UF_CAT3').show();
			// }
		// });
	// });
	
	// $('[name=REGISTER_FILES_PERSONAL_PHOTO]').change(function(){
		// alert($(this).val());
	// });
</script>