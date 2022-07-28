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

$res = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 2), false, false, array('ID','NAME',"PROPERTY_country_id"));
while($ar_res = $res -> GetNext()){
	$COUNTRY_ARRAY[$ar_res['PROPERTY_COUNTRY_ID_VALUE']] = $ar_res['NAME'];
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
<!--<?if($USER->isAdmin())pr($arResult);?>
<?if($USER->isAdmin())pr($_POST);?>-->




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

				$('#upload_photo').html('<img src="' + src + '" />');
				//$('#upload_photo').append('<input type="hidden"  name="file" value="' + src + '" />');
				$('[name=file]').val(src);
				$('#upload_photo').css('opacity','1.0');
				release();
				$('#upload_photo').css('opacity','1.0');
				jcrop_api.destroy();
			});
			
		});   
	});

	jQuery(document).ready(function () {            
		var uploader = new qq.FileUploader({
			element: document.getElementById('upload_photo'),
			debug: true,
			uploadButtonText: '<div style="width:100px;height:100px"></div>',
			onSubmit: function(){
				$('.photo').prepend("<p>&nbsp;&nbsp;Идёт загрузка</p>");
				console.log();
			},
			onComplete: function(id, fileName, responseJSON){
				$('#upload_photo').html('<img src="' + '<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName'] + '" />');
				//$('#upload_photo').append('<input type="hidden"  name="file" value="' + '<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName'] + '" />');
				$('[name=file]').val('<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName']);
				$('#upload_photo').css('opacity','1.0');
            },
			action: '/ajax/upload.php'
		});           
	});
</script>
<?if( strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')===false && strpos($_SERVER['HTTP_USER_AGENT'],'rv:11.0')===false):?>
<script type="text/javascript">
	$(function(){
		$("#show_popup_camera").fancybox();
		
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
			$('#show_popup_camera').trigger('click');
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
		<div id="step1" class="step">
			<div class="form-box">
				<p>Для участия в мероприятиях Форума  необходимо пройти регистрацию (все поля формы обязательны для заполнения). Для успешного завершения регистрации и сохранения данных в системе, пожалуйста, убедитесь, что у Вас  имеются нижеприведенные документы, отвечающие требованиям системы:  
				</p>
				<h3>Требования системы:</h3>
				<p>1. Личная фотография (для бейджа) - цветная фотография в формате JPG, изображение лица должно занимать не менее 70% фотографии, размер не более 3 мб.;
				</p><p>2. Электронная почта (для получения логина и пароля в Личный кабинет);
				</p><p>3. Паспортные данные.
				</p><p>После успешного завершения регистрации и утверждения участия Оргкомитетом Форума, Вы получите доступ в Личный кабинет для получения всей необходимой информации по участию в Форуме.
				</p>
			</div>
			<div class="form-box">
				<h3 class="info-block">Персональные данные</h3>
                <div class="row">
                    <label for="id3">ФИО</label>
                    <input type="text" name="UF_FIO" class="text" value="<?=$arResult["VALUES"]['UF_FIO']?>" err="Иванов Иван Иванович" placeholder = "Иванов Иван Иванович" id="id3">
                </div>
				<div class="row">
					<label for="id5">Дата рождения</label>
					<select name="d">
						<option></option>
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m'>
						<option></option>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y'>
						<option></option>
						<?for($i=date('Y');$i>1930;$i--):?>
							<option value="<?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<input id="PERSONAL_BIRTHDAY" type="hidden" name="REGISTER[PERSONAL_BIRTHDAY]" />
				</div>
                <div class="row">
                        <label id='UF_CAT1'>Категория участника</label>
                        <select name="UF_CAT1" class="select-large">
							<option value="">не указана</option>
							<?foreach($CATEGORY1 as $k => $v):?>
								<option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>
                </div>
                <div class="row">
                    <label for="id11">Мобильный телефон</label>
                    <input name='tmp_mobile' type="text" class="text text-small" value="<?=$arResult["VALUES"]['PERSONAL_MOBILE']?>" id="id11" err="+79037147415" placeholder="+79037147415">
                    <input id="PERSONAL_MOBILE" name='REGISTER[PERSONAL_MOBILE]' type="hidden" class="text text-small" value="" />
                </div>
                <div class="row">
                    <label for="id10">Электронная почта</label>
                    <input name="REGISTER[LOGIN]" type="text" class="text" value="<?=$arResult["VALUES"]['LOGIN']?>" id="id10" err="Ivanov@mail.ru" placeholder="Ivanov@mail.ru">
                </div>
                <div class="row">
                    <label>Регион</label>
                    <select name="UF_REG_SUBJECT" class="select-large">
                        <option value=""></option>
                        <option value="Москва">Москва</option>
                        <option value="Санкт-Петербург">Санкт-Петербург</option>
                        <?foreach($SUBJECT_ARRAY as $k => $v):?>
                            <option value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                    </select>
                </div>
                <div id="UF_REG_COUNTRY" class="row">
                    <label for="id102">Город/населенный пункт</label>
                    <input name="UF_REG_CITY" type="text" class="text" value="<?=$arResult["VALUES"]['UF_BIRTH_CITY']?>" placeholder="Москва" id="id102">
                </div>
                <div class="row">
                    <label for="id26">Место проживания</label>
                    <input name="UF_REG_STREET" type="text" class="text" value="<?=$arResult["VALUES"]['UF_REG_STREET']?>" err="Краснознаменская" placeholder="Краснознаменская" id="id26">
                </div>
                <div class="row">
                    <label for="id20">Серия и номер паспорта</label>
                    <input name="UF_PASSPORT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PASSPORT']?>" placeholder="4610 762874" err="4610 762874" id="id20">
                </div>
                <div class="row">
                    <label for="id5">Название организации</label>
                    <input name="REGISTER[WORK_COMPANY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COMPANY']?>" id="id5" />
                    <input name="UF_ORG_ID" type="hidden" class="text" value="" />
                </div>
                <div class="row">
                    <label for="id6">Должность</label>
                    <input type="text" name="REGISTER[WORK_POSITION]" class="text" value="<?=$arResult["VALUES"]['WORK_POSITION']?>" id="id6">
                </div>
                <div class="row">
                    <label for="id6">Пароль</label>
                    <input type="text" name="PASSWORD" class="text" value="<?=$arResult["VALUES"]['PASSWORD']?>" id="id6">
                </div>
                <div class="submit-wrap row">
                    <input id="register_submit_button" type="submit" name="register_submit_button" class="submit halfwidth" value="Завершить" />
                    <input type="hidden" name="register_submit_button" class="submit" value="Завершить" />
                    <span class="btn-note"></span>
                </div>






                <!-- STOP HERE -->



				<div class="divider"></div>
				
				<?if(empty($_REQUEST['new'])):?>
				<h3 class="info-block">Организация</h3>

				
				<div class="row">
					<label for="id95">Сайт компании</label>
					<input name="UF_WORK_WWW" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_WWW']?>" id="id95" />
				</div>
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

				<?if(!array_key_exists('new', $_REQUEST)):?>
				<div class="row">
					<div class="right-column">
						<input type="checkbox" name="UF_UPOLN" value="да" class="checkbox" id="id9" />
						<label class="fullwidth" for="id9">Я — уполномоченное доверенное лицо делегации</label>
					</div>
				</div>
				<div class="divider"></div>
				<div class="right-column" style="font-size:12px;color:#576570">
					<span>Уполномоченное доверенное лицо делегации:</span>
					<p style="font-size:12px;color:#576570">— может регистрировать всех делегатов в личном кабинете и вносить изменения;<br/>— может получить бейдж за делегацию по доверенности (образец доверенности вы можете скачать в личном кабинете)</p>
				</div>
				<?endif;?>
				<div class="divider"></div>
				<div class="row">
					<label>Загрузите личное фото*:</label>
					<div class="right-column-inline">
						<div class="download-wrap">
							<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
								<div id="upload_photo" class="photo"></div>
							<?endif?>
							<p>Цветная фотография на однородном фоне в формате JPG, размер фотографии не меньше чем 300х400 пикселей, фотография анфас, без головного убора, изображение лица должно занимать не менее 70% фотографии, размер не более 3 МБ.</p>
							<p><a href="https://www.sportdergava.ru/upload/iblock/54e/%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F-%D0%BA-%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%8F%D0%BC.pdf" target="_blank">Требования к фотографии</a></p>
							<p>*Организаторы Форума "Россия-спортивная держава" оставляют за собой право использовать "Личное фото" участников для размещения в разделе "Участники" на сайте <a href="http://sportforumrussia.ru/participation/" target="_blank"> sportforumrussia.ru </a></p>
						</div>

						<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
							<input type="hidden"  name="file" value="" />
							<button id="btn-start-camera" class="btn-photo">Сделать фото с веб-камеры</button>
						<?else:?>
							<input type="file" name="REGISTER_FILES_PERSONAL_PHOTO" />
						<?endif?>

					</div>
				</div>
				<h3 class="info-block">Контактная информация</h3>


				<div class="row">
					<div class="right-column">
						<input type="checkbox" class="checkbox" name="UF_SMS_AGREE" id="id15">
						<label class="fullwidth" for="id15">Согласен на получение СМС-уведомлений и электронных писем от Организатора</label>
					</div>
				</div>
				<div class="row">
					<div class="right-column">
                      
						<input type="checkbox" class="checkbox" id="id13">
						<label class="fullwidth" for="id13">Согласен на обработку своих персональных данных</label><br><br>
						<div style="border: 1px solid #cfcfcf; border-radius: 2px; height: 110px; overflow: auto; padding: 0.6em 0.9em;">
							В соответствии со статьей 9 Федерального закона от 27 июля 2006 года N 152-ФЗ "О персональных данных" даю своё согласие АНО «Форум «Спортивная Держава» (далее – Организатор) на обработку моих персональных данных Организатором в целях обеспечения безопасности для участия в Международном спортивном форуме «Россия – спортивная держава» (далее – Форум). <br><br>
							Я согласен предоставить информацию, относящуюся к моей личности: фамилия, имя, отчество, дата рождения, контактный телефон, адрес e-mail, паспортные данные и место регистрации уполномоченным специализированным учреждениям безопасности РФ на период подготовки и проведения Форума и подтверждаю, что давая такое согласие, я действую своей волей и в своем интересе. <br><br>
							Я уведомлен и понимаю, что под обработкой персональных данных подразумевается сбор, систематизация, накопление, хранение, уточнение (обновление, изменение), использование, распространение, уничтожение и любые другие действия в соответствии с действующим законодательством. Обработка данных может осуществляться с использованием средств автоматизации, так и без их использования (при неавтоматической обработке).
						</div>
					</div>
				</div>
				<div class="row">
					<div class="right-column">
                        <input id="register_submit_button" type="submit" name="register_submit_button" class="submit fullwidth check" value="Завершить" disabled='disabled' />
						<input type="submit" class="submit fullwidth go-step2" value="Далее" disabled='disabled' />
					</div>
				</div>
				<div class="row">
					<div class="right-column">
						<span class="btn-note"></span>
					</div>
				</div>
			</div>
		</div>
		<div id="step2" class="step" style="display:none">
			<div class="form-box">
				<h3 class="info-block">Паспортные данные</h3>
				<div class="row">
					<label for="id20">Серия и номер паспорта</label>
					<input name="UF_PASSPORT" type="text" class="text" value="<?=$arResult["VALUES"]['UF_PASSPORT']?>" placeholder="4610 762874" err="4610 762874" id="id20">
				</div>
				<div class="row">
					<label for="id22">Кем выдан (как в паспорте)</label>
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
				<h3 class="info-block">Адрес регистрации</h3>
				<div class="row">
					<label>Страна</label>
					<select name="UF_REG_COUNTRY" class="select-large">
						<?foreach($COUNTRY_ARRAY as $k => $v):?>
							<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
						<?endforeach;?>
					</select>
				</div>
				<div class="row">
					<label>Субъект РФ</label>
					<select name="UF_REG_SUBJECT" class="select-large">
						<option value=""></option>
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
				<h3 class="info-block">Место фактического проживания</h3>
				<div class="row">
					<div class="right-column">
						<input type="checkbox" name="SOVPAD" value="Y" class="checkbox" id="id900" />
						<label class="fullwidth" for="id900">Совпадает с адресом регистрации</label>
					</div>
				</div>
				<div id="mesto_proj" class="row">
					<div class="row">
						<label>Страна</label>
						<select name="UF_REAL_COUNTRY" class="select-large">
							<?foreach($COUNTRY_ARRAY as $k => $v):?>
								<option country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>
					</div>
					<div class="row">
						<label>Субъект РФ</label>
						<select name="UF_REAL_SUBJECT" class="select-large">
							<option value=""></option>
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
				</div>
				<div class="submit-wrap row">
					<input type="submit" class="submit halfwidth go-step1" value="Назад">
					<input type="submit" class="submit halfwidth go-step3" value="Далее">
					<span class="btn-note"></span>
				</div>
			</div>
		</div>
		<div id="step3" class="step" style="display:none">
			<h3 class="info-block">Выберите из списка категорию своего участия</h3>
			<div class="info-block"><div style="word-spacing:300px;"><h3>Шаг-1 Шаг-2</h3></div></div>
			<div class="form-box">
				<div class="row">
					<div class="column category-1" style="text-align: center">
						<label style="display:none" id='UF_CAT1'>Категория 1</label>
						<?/*<select name="UF_CAT1" class="select-large">
							<option value="">не указана</option>
							<?foreach($CATEGORY1 as $k => $v):?>
								<option sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
						</select>*/?>
						<?foreach($CATEGORY1 as $sectionId => $sectionName):?>
							<input id="category-1-<?=$sectionId?>" type="radio" name="UF_CAT1" sect="<?=$sectionId?>" value="<?=$sectionName?>"><label class="fullwidth" for="category-1-<?=$sectionId?>"><?=$sectionName?></label><br>
						<?endforeach;?>
					</div>
					<div class="column">
						<div id = "UF_CAT2" class="row" style="display:none;text-align: center">
							<label style="display:none">Категория 2></label>
							<?/*<select name="UF_CAT2" class="select-large" multiple>
								<option value="">не указана</option>
							</select>*/?>
							<div class="category-2">
							</div>
						</div>
						<div id = "UF_CAT3" style="display:none;text-align: center" class="row">
							<label style="display:none">Категория 3</label>
							<select name="UF_CAT3" class="select-large">
								<option value="">не указана</option>
							</select>				
						</div>
					</div>
				</div>
				<div class="submit-wrap row">
					<input type="submit" class="submit halfwidth go-step2" value="Назад" />
					<input type="submit" class="submit halfwidth go-step4" value="Далее" disabled />
					<span class="btn-note"></span>
				</div>
			</div>
		</div>
		<div id="step4" class="step" style="display:none">
			<h3 class="info-block">Проживание и детали проезда</h3>
			<h3 class="info-block">Проживание</h3>
			<p class="right-column">Для размещения Участников и гостей Форума были отобраны гостиницы, которые имеют наиболее удобное расположение относительно места проведения. 
Организаторы окажут Вам содействие в бронировании номеров в официальных гостиницах по специальному для Участников Форума тарифу. 
От официальных гостиниц  будет действовать система шаттлов до мест проведения мероприятий Форума.
Разъяснения о процедуре бронирования доступны в Личном кабинете.</p>
			<h3 class="info-block">Наземное транспортное обслуживание</h3>
			<p class="right-column">Для участников Форума будет организован трансфер от ж/д вокзала и аэропорта г. Екатеринбург до мест проживания 
и мест проведения деловой программы Форума. Для того, чтобы Вас смогли встретить и организовать эффективную транспортную логистику, не 
забудьте заполнить в системе данные о своем прибытии на Форум.</p>
			
			<div class="form-box">
				<h3 class="info-block">Укажите предварительные сроки поездки</h3>
				<div class="row">
					<label for="id5">Дата прибытия</label>
					<select name="d2">
						<?for ($i = 1; $i < 32; $i++):?>
							<option value="<?if($i < 2)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m2'>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y'>
						<option></option>
						<?for($i=date('Y');$i>1930;$i--):?>
							<option value="<?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					
					<input id="UF_DATE_IN" type="hidden" name="UF_DATE_IN" />
				</div>

				<div class="row">
					<label for="id5">Дата отправления</label>
					<select name="d3">
						<?for ($i = 1; $i < 32; $i++):?>
							<option value="<?if($i < 2)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m3'>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("f",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
						<?endfor?>
					</select>
					<select name='y'>
						<option></option>
						<?for($i=date('Y');$i>1930;$i--):?>
							<option value="<?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					
					<input id="UF_DATE_OUT" type="hidden" name="UF_DATE_OUT" />
				</div>

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
				<div class="row">
					<div class="right-column">
						<input name="UF_DISABLED_VEHICLE" value="1" type="checkbox" class="checkbox" id="id1271">
						<label class="fullwidth" for="id1271">Мне необходим специальный транспорт для передвижения людей с ограниченными возможностями</label>
					</div>
				</div>
				<div class="submit-wrap row">
					<input type="submit" class="submit halfwidth go-step3" value="Назад">
					<input id="register_submit_button" type="submit" name="register_submit_button" class="submit halfwidth" value="Завершить" />
					<input type="hidden" name="register_submit_button" class="submit" value="Завершить" />
					<span class="btn-note"></span>
				</div>
			</div>
		</div>
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
			<button id="snap" class="btn-photo">Сделать фото с веб-камеры</button>
			<button class="btn-photo crop-btn" id="crop">Обрезать</button>
			<button class="btn-photo crop-btn" id="crop-start">Редактировать</button>
			<button style="display:none" class="btn-photo" id="snap-else" class="btn-photo">Сделать новое фото</button>
			<button style="float:right;display:none;" id="snap-save" class="btn-photo">Сохранить</button>
		</p>
	</div>
</div>




<script>
	<?if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'],'rv:11.0') !== false):?>
		$('#btn-start-camera').remove();
	<?endif?>
	
	navigator.getUserMedia = ( navigator.getUserMedia ||
	   navigator.webkitGetUserMedia ||
	   navigator.mozGetUserMedia ||
	navigator.msGetUserMedia);
	
	if (!navigator.getUserMedia) {
		$('#btn-start-camera').remove();
	}
	
	$('[name=UF_TRANSPORT]').change(function(){
		if($('[name=UF_TRANSPORT] option:selected').val() == "Личный автомобиль"){
			$('#idhide').show();
		}
		else{
			$('#idhide').hide();
		}
	});
	$('[name = tmp_mobile]').change(function(){
		$('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val().replace("+7", "8"));
	});

	$("#id900").change(function(){
		$(this).toggleClass('checked');
		if($(this).hasClass('checked')){
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
		else{
			$('#mesto_proj').show();
		}
	});

	
	$(document).ready(function () {
		$("#show_popup").click();
		$('#id23').mask("99.99.9999");
		$('#id11').mask("+79999999999");
		$('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val().replace("+7", "8"));
	});
	function isValidEmail (email, strict){
		if ( !strict ) email = email.replace(/^s+|s+$/g, '');
		return (/^([a-z0-9_-]+.)*[a-z0-9_-]+@([a-z0-9][a-z0-9-]*[a-z0-9].)+[a-z]{2,4}$/i).test(email);
	}
	$('#id13').change(function(){
		if($('.check').attr('disabled')=='disabled')$('.check').attr('disabled', false);
		else $('.check').attr('disabled', 'disabled');
	});
	$('.go-step1').click(function(e){
		e.preventDefault();
		$('.step').hide();
		$('#step1').show();
	});
	$('.check').click(function(e){
		e.preventDefault();
		$('#step1 .btn-note').html('');
		var err = 0;
		var err_text = 'Вы не заполнили поля:<br/>';
		var birthday = $('[name=d]').val() + '.' + $('[name=m]').val() + '.' + $('[name=y]').val();
		$('#PERSONAL_BIRTHDAY').val(birthday);
		$('#step1 [type=text]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				err++;
				err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find('label').text() + '</a>&nbsp;';
			}
		});
		if(!isValidEmail($('#id10').val(),true) && $('#id10').val() != ''){
			err++;
			err_text += '<br><a href="#' + $('#id10').attr('id') + '">Не верный формат Email</a>';
		}
		
		if($('[name=UF_FIO_EN]').val() != '' && $('[name=UF_FIO_EN]').val() != $('[name=UF_FIO_EN]').attr('err')){
			var reg = /^[a-zA-Z0-9 .,]+$/;
			if(!reg.test($('[name=UF_FIO_EN]').val())){
				err++;
				err_text += '<br><a href="#id4">ФИО на латинском - Разрешены только англ. буквы</a>';
			}
		}
		if($('[name=UF_COMPANY_EN]').val() != '' && $('[name=UF_COMPANY_EN]').val() != $('[name=UF_COMPANY_EN]').attr('err')){
			var reg = /^[a-zA-Z0-9 .,'";/_:-]+$/;
			if(!reg.test($('[name=UF_COMPANY_EN]').val())){
				err++;
				err_text += '<br><a href="#id555">Организация на латинском - Разрешены только англ. буквы</a>';
			}
		}
		if($('[name=UF_FIO]').val() != '' && $('[name=UF_FIO]').val() != $('[name=UF_FIO]').attr('err')){
			var reg = /^[а-яА-Я0-9 .,]+$/;
			if(!reg.test($('[name=UF_FIO]').val())){
				err++;
				err_text += '<br><a href="#id3">ФИО - Разрешены только русские буквы</a>';
			}
		}
		<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
		if($('[name=file]').val() == ""){
			err++;
			err_text += '<br><a href="#upload_photo">Личное фото</a>';
		}
		<?endif?>
		$.post('/ajax/get_email.php', {EMAIL: $('#id10').val()}, function(data){
			if(data=='ok'){
				err++;
				err_text += '<br><a href="#' + $('#id10').attr('id') + '">Данный Email уже существует</a>';
			}
			if(err==0){
				$('.step').hide();
				$('#step2').show();
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
		var err_text = 'Вы не заполнили поля:<br/>';
		$('#step2 [type=text]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				if($(this).attr('name') != "UF_NUM_ACCRED" && $(this).attr('name') != "UF_REG_SECTOR" && $(this).attr('name') != "UF_REAL_SECTOR" && $(this).attr('name') != "UF_REG_CORPUS" && $(this).attr('name') != "UF_REAL_CORPUS"){
					err++;
					err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find('label').text() + '</a>&nbsp;';
				}
			}
		});
		if(err==0){
			$('.step').hide();
			$('#step3').show();
			$('html, body').animate({scrollTop: 0}, 700);
		}
		else{
			$('#step2 .btn-note').html(err_text);
		}
	});
	$('.go-step4').click(function(e){
		e.preventDefault();
		$('#step3 .btn-note').html('');
		var err = 0;
		var err_text = 'Вы не заполнили поля:<br/>';
		// if ($('[name=UF_CAT1]').val() == "" || $(this).val() == $(this).attr('err')) {
		if ($('[name=UF_CAT1]:checked').val() == "" || $(this).val() == $(this).attr('err')) {
				err++;
				err_text += '<a href="#UF_CAT1">Категория 1</a>';			
		}
		if(err==0){
			$('.step').hide();
			$('#step4').show();
			$('html, body').animate({scrollTop: 0}, 700);
		}
		else{
			$('#step3 .btn-note').html(err_text);
		}
	});
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
	
	$('[name=UF_REAL_COUNTRY]').change(function(){
		if($('[name=UF_REAL_COUNTRY] option:selected').attr('country') == 3159){
			$('#UF_REAL_COUNTRY').prev().show();
		}
		else{
			$('#UF_REAL_COUNTRY').prev().hide();
		}
		/*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_REAL_COUNTRY] option:selected').attr('country')}, function(data){
			data = jQuery.parseJSON(data);
			$('#UF_REAL_COUNTRY .select-large').html('');
			for(var i=0;i<data.length;i++){
				$('#UF_REAL_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			}
		});*/
	});
	
	$('[name=UF_BIRTH_COUNTRY]').change(function(){
		if($('[name=UF_BIRTH_COUNTRY] option:selected').attr('country') == 3159){
			$('#UF_BIRTH_COUNTRY').prev().show();
		}
		else{
			$('#UF_BIRTH_COUNTRY').prev().hide();
		}
		/*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_BIRTH_COUNTRY] option:selected').attr('country')}, function(data){
			data = jQuery.parseJSON(data);
			$('#UF_BIRTH_COUNTRY .select-large').html('');
			for(var i=0;i<data.length;i++){
				$('#UF_BIRTH_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			}
		});*/
	});
	
	$('[name=UF_REG_COUNTRY]').change(function(){
		if($('[name=UF_REG_COUNTRY] option:selected').attr('country') == 3159){
			$('#UF_REG_COUNTRY').prev().show();
		}
		else{
			$('#UF_REG_COUNTRY').prev().hide();
		}
		/*$.post('/ajax/get_city.php', {COUNTRY: $('[name=UF_REG_COUNTRY] option:selected').attr('country')}, function(data){
			data = jQuery.parseJSON(data);
			$('#UF_REG_COUNTRY .select-large').html('');
			for(var i=0;i<data.length;i++){
				$('#UF_REG_COUNTRY .select-large').append('<option value="' + data[i] + '">' + data[i] + '</option>');
			}
		});*/
	});
	
	$('[name=UF_CAT1]').change(function(){
		// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT1] option:selected').attr('sect')}, function(data){
		$.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT1]:checked').attr('sect')}, function(data){
			
			data = jQuery.parseJSON(data);
			// $('[name=UF_CAT2]').html('');
			$('.category-2').html('');
			$('.go-step4').attr('disabled', 'disabled');
			$('[name=UF_CAT3]').html('');
			if (data != undefined && typeof data == 'object' && 'ID' in data && typeof data.ID == 'object' && 'length' in data.ID) {
				for(var i=0;i<data.ID.length;i++){
					// $('[name=UF_CAT2]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
					$('.category-2').append('<input id="category-2-' + data.ID[i] + '" type="radio" name="UF_CAT2" sect="' + data.ID[i] + '" value="' + data.NAME[i] + '"><label class="fullwidth" for="category-2-' + data.ID[i] + '">' + data.NAME[i] + '</label><br>');
				}
				$('#UF_CAT2').show();
				jcf.customForms.replaceAll();
			}
			$('#UF_CAT3').hide();
			// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2] option:selected').attr('sect')}, function(data){
			$.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2]:checked').attr('sect')}, function(data){
				data = jQuery.parseJSON(data);
				$('[name=UF_CAT3]').html('');
				if (data != undefined && typeof data == 'object' && 'ID' in data && typeof data.ID == 'object' && 'length' in data.ID) {
					for(var i=0;i<data.ID.length;i++){
						$('[name=UF_CAT3]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
						$('#UF_CAT3').show();
					}
				}
			});
		});
	});
	
	// $('[name=UF_CAT2]').change(function(){
	$('.category-2').on('change', '[name=UF_CAT2]', function(){
		$.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2]:checked').attr('sect')}, function(data){
			$('.go-step4').removeAttr('disabled');
			data = jQuery.parseJSON(data);
			$('[name=UF_CAT3]').html('');
			if (data != undefined && typeof data == 'object' && 'ID' in data && typeof data.ID == 'object' && 'length' in data.ID) {
				for(var i=0;i<data.ID.length;i++){
					$('[name=UF_CAT3]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
					$('#UF_CAT3').show();
				}
			}
		});
	});
	
	$('[name=REGISTER_FILES_PERSONAL_PHOTO]').change(function(){
		alert($(this).val());
	});
</script>