<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/litranslit.js');



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
$COUNTRY_ARRAY_EN = array();
$SUBJECT_ARRAY = array();
$CATEGORY_RUS = array();
$CATEGORY_ENG = array();
$TRANSPORT = array();

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'ACTIVE' => 'Y', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
	$CATEGORY_RUS[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockSection::GetList(array(),array("IBLOCK_ID" => 4, 'ACTIVE' => 'N', 'SECTION_ID' => false), false, array('ID','NAME'));
while($ar_res = $res -> GetNext()){
    $CATEGORY_ENG[$ar_res['ID']] = $ar_res['NAME'];
}

$res = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 2), false, false, array('ID','NAME',"PROPERTY_country_id"));
while($ar_res = $res -> GetNext()){
	$COUNTRY_ARRAY[$ar_res['PROPERTY_COUNTRY_ID_VALUE']] = $ar_res['NAME'];
}

$res = CIBlockElement::GetList(array('NAME'=>'ASC'),array("IBLOCK_ID" => 2, 'ACTIVE' => 'Y'), false, false, array('ID','NAME',"PROPERTY_country_id"));
while($ar_res = $res -> GetNext()){
    //$COUNTRY_ARRAY[$ar_res['PROPERTY_COUNTRY_ID_VALUE']] = $ar_res['NAME'];
    $COUNTRY_ARRAY_EN[$ar_res['ID']] = $ar_res['NAME'];
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
                console.log(file);
				$('.jcrop-holder img').attr('src' , src);
				$('#target').attr('src' , src);
				$('#target').removeAttr("width");
				$('#target').removeAttr("height");
				$('#target').attr("style",'');
				$('.fancybox-inner').attr("style",'overflow: visible; width: 700px');

				$('#upload_photo').html('<img id="user_photo" src="' + src + '" />');

				//$('#upload_photo').append('<input type="hidden"  name="file" value="' + src + '" />');
				$('[name=file]').val(src);
				$('#upload_photo').css('opacity','1.0');
				release();
				$('#upload_photo').css('opacity','1.0');
				jcrop_api.destroy();


			});
			
		});   
	});



    // $('#user_photo').faceDetection({
    //     complete: function (faces) {
    //         console.log(faces);
    //         console.log(faces[0]);
    //         if (faces[0] == null) {
    //             alert('На фото нет лица!');
    //         }
    //         for (var i = 0; i < faces.length; i++) {
    //             $('<div>', {
    //                 'class': 'face',
    //                 'css': {
    //                     'position': 'absolute',
    //                     'left': faces[i].x * faces[i].scaleX + 'px',
    //                     'top': faces[i].y * faces[i].scaleY + 'px',
    //                     'width': faces[i].width * faces[i].scaleX + 'px',
    //                     'height': faces[i].height * faces[i].scaleY + 'px'
    //                 }
    //             })
    //                 .insertAfter(this);
    //         }
    //     },
    //     error: function (code, message) {
    //         alert('Error: ' + message);
    //     }
    // });



	jQuery(document).ready(function () {
		var uploader = new qq.FileUploader({
			element: document.getElementById('upload_photo'),
			debug: true,
			uploadButtonText: '<div style="width:100px;height:100px"></div>',
			onSubmit: function(){
			    let child = $('.load_time').length;
                if(!!child) {
                    $('.load_time').text('Идёт загрузка');
                } else {
                    $('.photo').prepend("<p class='load_time'>&nbsp;&nbsp;Идёт загрузка</p>");
                }
			},
			//onComplete: function(id, fileName, responseJSON){
            //    if (responseJSON.success) {
            //        $('#upload_photo').html('<img src="' + '<?//="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>//' + responseJSON['fileName'] + '" />');
            //        //$('#upload_photo').append('<input type="hidden"  name="file" value="' + '<?//="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>//' + responseJSON['fileName'] + '" />');
            //        $('[name=file]').val('<?//="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>//' + responseJSON['fileName']);
            //        $('#upload_photo').css('opacity','1.0');
            //    } else {
            //        $('.load_time').text('Не обнаружено лицо на фото');
            //    }
            //
            //},
			//action: '/php-facedetection-master/index.php'
            onComplete: function(id, fileName, responseJSON){
                $('#upload_photo').html('<img id="user_photo" src="' + '<?="/upload/users_photo/".$_SESSION['fixed_session_id'].'/'?>' + responseJSON['fileName'] + '" />');
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
<input id="rulang" type="button" value="Русский">
<input id="enlang" type="button" value="English">
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
			<div class="form-box rus">
				<p>Для участия в мероприятиях Форума  необходимо пройти регистрацию (все поля формы обязательны для заполнения). Для успешного завершения регистрации и сохранения данных в системе, пожалуйста, убедитесь, что у Вас  имеются нижеприведенные документы, отвечающие требованиям системы:  
				</p>
				<h3>Требования системы:</h3>
				<p>1. Личная фотография (для бейджа) - цветная фотография в формате JPG, изображение лица должно занимать не менее 70% фотографии, размер не более 3 мб.;
				</p><p>2. Электронная почта (для получения логина и пароля в Личный кабинет);
				</p><p>3. Паспортные данные.
				</p><p>После успешного завершения регистрации и утверждения участия Оргкомитетом Форума, Вы получите доступ в Личный кабинет для получения всей необходимой информации по участию в Форуме.
				</p>
			</div>
            <div class="form-box eng hidden">
                <p>
                    “Russia - country of sports” is the largest platform for interaction between participants and a landmark event in the sports and social and political life of the country. This aims at creating a platform through which constructive dialogue between the government and future generations can take place. It helps better understand and react faster to the rapidly changing environment in the sports industry.
                </p>
                <p>
                    Traditionally, the Forum is attended by international and all-Russian sports federations, heads of constituent entities of the Russian Federation, representatives of federal executive authorities, large business structures, famous athletes, industry experts and leaders of the sports industry.
                </p>
                <h3>Requirements:</h3>
                <p>
                    1. Personal photo (color photo in JPG format, face image must occupy at least 70% of the photo, size no more than 3 mb.);
                </p>
                <p>
                    2. Email (to receive a username and password in the Personal Account);
                </p>
                <p>
                    3. Passport data
                </p>
            </div>
			<div class="form-box">
				<h3 class="info-block rus">Персональные данные</h3>
                <h3 class="info-block eng hidden">Personal data</h3>
                <div class="row">
                    <span class="label">
                    <label for="surname_custom" class="rus ruserr">Фамилия</label>
                    <label for="surname_custom" class="eng hidden engerr">First Name</label>
                    </span>
                    <input type="text" name="" class="text" value="" id="surname_custom">
                </div>
                <div class="row">
                    <label for="name_custom" class="rus">Имя</label>
                    <label for="name_custom" class="eng hidden">Name</label>
                    <input type="text" name="" class="text" value="" id="name_custom">
                </div>
                <div class="row">
                    <label for="middle_name_custom" class="rus">Отчество</label>
                    <label for="middle_name_custom" class="eng hidden">Middle name</label>
                    <input name="" class="text" value="<?=$arResult["VALUES"]['UF_FIO']?>" id="middle_name_custom">
                </div>
                <input type="hidden" name="UF_FIO" class="text" value="<?=$arResult["VALUES"]['UF_FIO']?>" err="Иванов Иван Иванович" placeholder = "Иванов Иван Иванович" id="id3">
                <div class="row rus">
                    <label for="id4">Имя, фамилия латинскими буквами (для бейджа):</label>
                    <input type="text" name="UF_FIO_EN" class="text" value="<?=$arResult["VALUES"]['UF_FIO_EN']?>" err="Ivanov Ivan" placeholder = "" id="id4">
                </div>
				<div class="row">
					<label for="id5" class="rus">Дата рождения</label>
                    <label for="id5" class="eng hidden"> Date of birth</label>
					<select name="d">
						<option></option>
						<?for($i=1;$i<32;$i++):?>
							<option value="<?if($i < 10)echo '0';?><?=$i?>"><?=$i?></option>
						<?endfor?>
					</select>
					<select name='m'>
						<option></option>
						<?for($i=2;$i<14;$i++):?>
							<option value="<?if($i-1 < 10)echo '0';?><?=$i-1?>"><?=FormatDate("m",  mktime(0, 0, 0, $i  , 0, 2000));?></option>
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
                        <label id='UF_CAT1' class="rus">Категория участника</label>
                        <label id='UF_CAT1' class="eng hidden">Category</label>
                        <select name="UF_CAT1" class="select selectcat">
							<?foreach($CATEGORY_RUS as $k => $v):?>
								<option class="ruscat" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
							<?endforeach;?>
                            <?foreach($CATEGORY_ENG as $k => $v):?>
                               <option class="engcat" sect="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                            <?endforeach;?>
						</select>
                </div>
                <div class="row">
                    <label for="id11" class="rus">Мобильный телефон</label>
                    <label for="id11" class="eng hidden">Phone</label>
                    <input name='tmp_mobile' type="text" class="text text-small" value="<?=$arResult["VALUES"]['PERSONAL_MOBILE']?>" id="id11" err="+79037147415">
                    <input id="PERSONAL_MOBILE" name='REGISTER[PERSONAL_MOBILE]' type="hidden" class="text text-small" value="" />
                </div>
                <div class="row">
                    <label for="id10" class="rus">Электронная почта</label>
                    <label for="id10" class="eng hidden">E-mail</label>
                    <input name="REGISTER[LOGIN]" type="text" class="text" value="<?=$arResult["VALUES"]['LOGIN']?>" id="id10" err="Ivanov@mail.ru" placeholder="">
                </div>
                <div class="row">
                    <label class="rus">Страна</label>
                    <label class="eng hidden">Country</label>
                    <select name="UF_REG_COUNTRY" class="select-large selectcountry" id="placeholder">
                        <?foreach($COUNTRY_ARRAY as $k => $v):?>
                           <option class="ruscontry" country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                        <?foreach($COUNTRY_ARRAY_EN as $k => $v):?>
                           <option class="engcontry" country="<?=$k?>" value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                    </select>
                </div>
                <div class="row rus region">
                    <label for="id12" class="rus">Регион</label>
                    <select name="UF_REGION" class="select-large">
                        <option value=""></option>
                        <option value="Москва">Москва</option>
                        <option value="Санкт-Петербург">Санкт-Петербург</option>
                        <?foreach($SUBJECT_ARRAY as $k => $v):?>
                            <option value="<?=$v?>"><?=$v?></option>
                        <?endforeach;?>
                    </select>
                </div>
                <div class="row">
                    <label for="id14" class="rus">Город</label>
                    <label for="id14" class="eng hidden">City</label>
                    <input name='UF_CITY' id="UF_CITY" class="text" type="text" value="<?=$arResult["VALUES"]['UF_CITY']?>" placeholder="" />
                </div>
                <div class="row rus">
                    <label for="id15">Место проживания</label>
                    <input name='UF_ADDRESS' id="UF_ADDRESS" class="text" type="text" value="<?=$arResult["VALUES"]['UF_ADDRESS']?>" placeholder="" />
                </div>
                <div class="row">
                    <label for="id15" class="rus">Паспортные данные</label>
                    <label for="id15" class="eng hidden">Passport number</label>
                    <input name='UF_PASSPORT' class="text" type="text" value="<?=$arResult["VALUES"]['UF_PASSPORT']?>" placeholder="" />
                </div>
                <div class="row">
                    <label for="id5" class="rus">Название организации</label>
                    <label for="id5" class="eng hidden">Organization / Company</label>
                    <input name="REGISTER[WORK_COMPANY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COMPANY']?>" id="id5" />
                    <input name="UF_ORG_ID" type="hidden" class="text" value="" />
                </div>
                <div class="row">
                    <label for="id6" class="rus">Должность</label>
                    <label for="id6" class="eng hidden">Position</label>
                    <input type="text" name="REGISTER[WORK_POSITION]" class="text" value="<?=$arResult["VALUES"]['WORK_POSITION']?>" id="id6">
                </div>
                <div class="row">
                    <label class="rus">Загрузите личное фото*:</label>
                    <label class="eng hidden">Photo*:</label>
                    <div class="right-column-inline">
                        <div class="download-wrap">
                            <?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
                                <div id="upload_photo" class="photo"></div>
                            <?endif?>
                            <p class="rus">Цветная фотография на однородном фоне в формате JPG, размер фотографии не меньше чем 300х400 пикселей, фотография анфас, без головного убора, изображение лица должно занимать не менее 70% фотографии, размер не более 3 МБ.</p>
                            <p class="eng hidden">The photo must be in the .jpg or .jpeg format. The image file size should be up to 512 KB for upload to the Forum Online Accreditation Platform with the resolution of 420 pixels wide by 525 pixels height (proportions 4:5). Another size, format or proportion will not be acceptable by Forum Online Accreditation Platform</p>
                            <p><a href="https://www.sportdergava.ru/upload/iblock/54e/%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F-%D0%BA-%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%8F%D0%BC.pdf" target="_blank">Требования к фотографии</a></p>
                            <p class="rus">*Организаторы Форума "Россия-спортивная держава" оставляют за собой право использовать "Личное фото" участников для размещения в разделе "Участники" на сайте <a href="http://sportforumrussia.ru/participation/" target="_blank"> sportforumrussia.ru </a></p>
                            <p class="eng hidden">The organizers of the Forum reserve the right to use the "Personal photo" of the participants for posting in the "Participants" section of the website. </p>
                        </div>

                        <?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
                            <input type="hidden"  name="file" value="" />
<!--                            <button id="btn-start-camera" class="btn-photo">Сделать фото с веб-камеры</button>-->
                        <?else:?>
                            <input type="file" name="REGISTER_FILES_PERSONAL_PHOTO" />
                        <?endif?>
                    </div>
                </div>
                <div class="row">
                    <label for="id19" class="rus">Статус о вакцинации от Covid-19</label>
                    <label for="id19" class="eng hidden">Vaccine Status</label>
                    <select name="UF_VACCINE" class="select-large vaccine">
                        <option class="engvac" value="Not vaccinated">Not vaccinated</option>
                        <option class="engvac" value="Scheduled">Scheduled</option>
                        <option class="engvac" value="1st Dose done">1st Dose done</option>
                        <option class="engvac" value="Completed Both Dose">Completed Both Dose</option>
                        <option class="rusvac" value="Не вакцинирован">Не вакцинирован</option>
                        <option class="rusvac" value="Запланирована">Запланирована</option>
                        <option class="rusvac" value="Первый этап вакцинации пройден">Первый этап вакцинации пройден</option>
                        <option class="rusvac" value="Вакцинирован">Вакцинирован</option>
                        <option class="rusvac" value="Справка о медицинском отводе от прививки">Справка о медицинском отводе от прививки</option>
                    </select>
                </div>
                <div class="row scan" style="display: none">
                    <label class="rus">Документ о вакцинации</label>
                    <label class="eng hidden">Attach the file</label>
                    <input type="file" name="UF_PASSPORT_SCAN" />
                </div>
                <input type="hidden" class="ruslangfield" name="UF_RUS_LANG" type="text">
                <input type="hidden" class="englangfield" name="UF_ENG_LANG" type="text" value="hidden">

                <div class="row">
                    <input type="checkbox" class="checkbox" id="id13">
                    <label class="fullwidth rus" for="id13">Согласен на обработку своих персональных данных в соответствии с <a href="/upload/privacy_policy.pdf" target="_blank">Политикой конфиденциальности</a> и <a href="/upload/privacy_policy_Tatarstan.pdf" target="_blank">Решением Кабинета Министров Республики Татарстан</a></label>
                    <label class="fullwidth eng hidden" for="id13">I agree to the processing of my personal data in accordance with the <a href="/upload/privacy_policy.pdf" target="_blank">Privacy Policy</a> and the <a href="/upload/privacy_policy_Tatarstan.pdf" target="_blank">Decision of the Cabinet of Ministers of the Republic of Tatarstan</a></label><br><br>
                    <input id="UF_STATUS" name='UF_STATUS' type="hidden" class="text text-small" value="39" />
                </div>
                <div class="submit-wrap row">
                    <!--					<input type="submit" class="submit halfwidth go-step3" value="Назад">-->
                    <input id="register_submit_button" type="submit" name="register_submit_button" class="submit fullwidth check rus" value="Отправить заявку" disabled="disabled" />
                    <input id="register_submit_button" type="submit" name="register_submit_button" class="submit fullwidth check eng hidden" value="Register" disabled="disabled" />
                    <input type="hidden" name="register_submit_button" class="submit" value="Завершить" />
                    <span class="btn-note rus"></span>
                    <span class="btn-note-eng eng hidden"></span>
                </div>

				<div class="divider"></div>
			</div>
		</div>
<!--		<div id="step4" class="step">-->
<!--			<div class="form-box">-->
<!--                <div class="row">-->
<!--                        <input type="checkbox" class="checkbox" id="id13">-->
<!--                        <label class="fullwidth" for="id13">Согласен на обработку своих персональных данных</label><br><br>-->
<!--                        <div style="border: 1px solid #cfcfcf; border-radius: 2px; height: 110px; overflow: auto; padding: 0.6em 0.9em;">-->
<!--                            В соответствии со статьей 9 Федерального закона от 27 июля 2006 года N 152-ФЗ "О персональных данных" даю своё согласие АНО «Форум «Спортивная Держава» (далее – Организатор) на обработку моих персональных данных Организатором в целях обеспечения безопасности для участия в Международном спортивном форуме «Россия – спортивная держава» (далее – Форум). <br><br>-->
<!--                            Я согласен предоставить информацию, относящуюся к моей личности: фамилия, имя, отчество, дата рождения, контактный телефон, адрес e-mail, паспортные данные и место регистрации уполномоченным специализированным учреждениям безопасности РФ на период подготовки и проведения Форума и подтверждаю, что давая такое согласие, я действую своей волей и в своем интересе. <br><br>-->
<!--                            Я уведомлен и понимаю, что под обработкой персональных данных подразумевается сбор, систематизация, накопление, хранение, уточнение (обновление, изменение), использование, распространение, уничтожение и любые другие действия в соответствии с действующим законодательством. Обработка данных может осуществляться с использованием средств автоматизации, так и без их использования (при неавтоматической обработке).-->
<!--                        </div>-->
<!--                </div>-->
                <!--				<div class="row">-->
                <!--					<div class="right-column">-->
                <!--                        <input id="register_submit_button" type="submit" name="register_submit_button" class="submit fullwidth check" value="Завершить" disabled='disabled' />-->
                <!--						<input type="submit" class="submit fullwidth go-step2" value="Далее" disabled='disabled' />-->
                <!--					</div>-->
                <!--				</div>-->
<!--                <div class="row">-->
<!--                    <div class="right-column">-->
<!--                        <span class="btn-note"></span>-->
<!--                    </div>-->
<!--                </div>-->
<!--				<div class="submit-wrap row">-->
<!--					<input type="submit" class="submit halfwidth go-step3" value="Назад">-->
<!--					<input id="register_submit_button" type="submit" name="register_submit_button" class="submit fullwidth check" value="Отправить заявку" disabled="disabled" />-->
<!--					<input type="hidden" name="register_submit_button" class="submit" value="Завершить" />-->
<!--					<span class="btn-note"></span>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
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



    $(document).ready(function(){
        let cacheDomRuCat = $('.ruscat');
        let cacheDomEnCat = $('.engcat');
        let cacheDomRuContry = $('.ruscontry');
        let cacheDomEnContry = $('.engcontry');
        let cacheDomRuVac = $('.rusvac');
        let cacheDomEnVac = $('.engvac');
        let cacheRusErr = $('.ruserr');
        let cacheEngErr = $('.engerr');
        $('.engcat').remove();
        $('.engcontry').remove();
        $('.engvac').remove();
        $('#enlang').click(function(){
            cacheDomRuCat.remove();
            $('.selectcat').append(cacheDomEnCat);
            cacheDomRuContry.remove();
            $('.selectcountry').append(cacheDomEnContry);
            cacheDomRuVac.remove();
            $('.vaccine').append(cacheDomEnVac);
        });
        $('#rulang').click(function(){
            cacheDomEnCat.remove();
            $('.selectcat').append(cacheDomRuCat);
            cacheDomEnContry.remove();
            $('.selectcountry').append(cacheDomRuContry);
            cacheDomEnVac.remove();
            $('.vaccine').append(cacheDomRuVac);
        });
    });


    $('#surname_custom, #name_custom').on('input', function(){
        let surname_custom = $('#surname_custom').val();
        let name_custom = $('#name_custom').val();
        $('#id4').val( name_custom + ' ' + surname_custom );
        $('#id4').liTranslit({
            elAlias: $('#id4')
        });
    });

    $('#UF_CITY').on('input', function(){
        let UF_CITY = $('#UF_CITY').val();
        $('#UF_ADDRESS').val( UF_CITY + ', ');
        elAlias: $('#UF_ADDRESS');
    });

    let undo = $('.rusf')

    $('#enlang').click(function(){
        $('.rus').addClass('hidden').attr("hidden",true);
        // $(".rusf").remove();
        $('.eng').removeClass('hidden').attr("hidden",false);
        $('.ruslangfield').attr("value","hidden");
        $('.englangfield').removeAttr("value");

    });
    $('#rulang').click(function(){
        $('.eng').addClass('hidden').attr("hidden",true);
        $('.rus').removeClass('hidden').attr("hidden",false);
        // $('#placeholder').html(undo);
        $('.ruslangfield').removeAttr("value");
        $('.englangfield').attr("value","hidden");
    });

    $('[name=UF_CITY]').val();
    $('[name=UF_ADDRESS]').val();
    $('[name=UF_PASSPORT]').val();

    $('[name=UF_VACCINE]').change(function() {
        if($(this).find("option:selected").val() == "Вакцинирован") {
            $(".scan").show();
        } else if ($(this).find("option:selected").val() == "Справка о медицинском отводе от прививки") {
            $(".scan").show();
        } else if ($(this).find("option:selected").val() == "Первый этап вакцинации пройден") {
            $(".scan").show();
        } else if($(this).find("option:selected").val() == "Completed Both Dose") {
            $(".scan").show();
        } else if ($(this).find("option:selected").val() == "1st Dose done") {
            $(".scan").show();
        } else {
            $(".scan").hide();
        };
    });

    $('[name=UF_REG_COUNTRY]').change(function() {
        if($(this).find("option:selected").val() == "Россия") {
            $(".region").show();
        } else {
            $(".region").hide();
        };
    });
	
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
	// $('[name = tmp_mobile]').change(function(){
	// 	$('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val().replace("+7", "8"));
	// });

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
		//$('#id11').mask("+99999999999");
        // let middle = $('#id11').val();
		// $('#PERSONAL_MOBILE').val($('[name = tmp_mobile]').val());
        //$('#PERSONAL_MOBILE').val($('#id11').val());
		// .val().replace("+7", "8")
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
        var err_text_en = 'You have not filled in the fields:<br/>';
		var birthday = $('[name=d]').val() + '.' + $('[name=m]').val() + '.' + $('[name=y]').val();
		$('#PERSONAL_BIRTHDAY').val(birthday);
		$('#step1 [type=text]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				err++;
				err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find($("label.rus")).text() + '</a>&nbsp;';
                err_text_en += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find($("label.eng")).text() + '</a>&nbsp;';
			}
		});
		if(!isValidEmail($('#id10').val(),true) && $('#id10').val() != ''){
			err++;
			err_text += '<br><a href="#' + $('#id10').attr('id') + '">Не верный формат Email</a>';
            err_text_en += '<br><a href="#' + $('#id10').attr('id') + '">Wrong format Email</a>';
		}
		
		if($('[name=UF_FIO_EN]').val() != '' && $('[name=UF_FIO_EN]').val() != $('[name=UF_FIO_EN]').attr('err')){
			var reg = /^[a-zA-Z0-9 .,-]+$/;
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
			var reg = /^[а-яА-Яa-zA-Z .,-]+$/;
			if(!reg.test($('[name=UF_FIO]').val())){
				err++;
				err_text += '<br><a href="#id3">ФИО - Разрешены только русские и латинские буквы буквы</a>';
                err_text_en += '<br><a href="#id3">Last name, Name - only Russian and Latin letters are allowed</a>';
			}
		}
		<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
		if($('[name=file]').val() == ""){
			err++;
			err_text += '<br><a href="#upload_photo">Личное фото</a>';
            err_text_en += '<br><a href="#upload_photo">Personal photo</a>';
		}
		<?endif?>
		$.post('/ajax/get_email.php', {EMAIL: $('#id10').val()}, function(data){
			if(data=='ok'){
				err++;
				err_text += '<br><a href="#' + $('#id10').attr('id') + '">Данный Email уже существует</a>';
			}
			// if(err==0){
			// 	$('.step').hide();
			// 	$('#step2').show();
			// 	$('html, body').animate({scrollTop: 0}, 700);
			// }
            if(err==0){
                $('.reg-form').submit();
            }
			else{
				$('#step1 .btn-note').html(err_text);
                $('#step1 .btn-note-eng').html(err_text_en);

			}
		});
	});

	$('#register_submit_button').click(function(e){
		e.preventDefault();
        // if ($('[name=UF_CAT1]:checked').val() == "" || $(this).val() == $(this).attr('err')) {
        //     err++;
        //     err_text += '<a href="#UF_CAT1">Категория 1</a>';
        // }
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
		var UF_DATE_OUT = $('[name=d3]').val() + '.' + $('[name=m3]').val() + '.' + $('[name=y3]').val();
		$('#UF_DATE_OUT').val(UF_DATE_OUT);
		var UF_DATE_IN = $('[name=d2]').val() + '.' + $('[name=m2]').val() + '.' + $('[name=y2]').val();
		$('#UF_DATE_IN').val(UF_DATE_IN);
		
		$('#step1 .btn-note').html('');
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
		if($('[name=UF_TRANSPORT] option:selected').val() == "Личный автомобиль" && $('[name=UF_AUTO_NUM]').val() == ''){
			err++;
			err_text += '<a href="#UF_AUTO_NUM">Номер автомобиля</a>';	
		}
		// if(err==0){
		// 	$('.reg-form').submit();
		// }
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
	
	// $('[name=UF_CAT1]').change(function(){
	// 	// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT1] option:selected').attr('sect')}, function(data){
	// 	$.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT1]:checked').attr('sect')}, function(data){
	//
	// 		data = jQuery.parseJSON(data);
	// 		// $('[name=UF_CAT2]').html('');
	// 		$('.category-2').html('');
	// 		$('.go-step4').attr('disabled', 'disabled');
	// 		$('[name=UF_CAT3]').html('');
	// 		if (data != undefined && typeof data == 'object' && 'ID' in data && typeof data.ID == 'object' && 'length' in data.ID) {
	// 			for(var i=0;i<data.ID.length;i++){
	// 				// $('[name=UF_CAT2]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
	// 				$('.category-2').append('<input id="category-2-' + data.ID[i] + '" type="radio" name="UF_CAT2" sect="' + data.ID[i] + '" value="' + data.NAME[i] + '"><label class="fullwidth" for="category-2-' + data.ID[i] + '">' + data.NAME[i] + '</label><br>');
	// 			}
	// 			$('#UF_CAT2').show();
	// 			jcf.customForms.replaceAll();
	// 		}
	// 		$('#UF_CAT3').hide();
	// 		// $.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2] option:selected').attr('sect')}, function(data){
	// 		$.post('/ajax/get_category.php', {SECTION: $('[name=UF_CAT2]:checked').attr('sect')}, function(data){
	// 			data = jQuery.parseJSON(data);
	// 			$('[name=UF_CAT3]').html('');
	// 			if (data != undefined && typeof data == 'object' && 'ID' in data && typeof data.ID == 'object' && 'length' in data.ID) {
	// 				for(var i=0;i<data.ID.length;i++){
	// 					$('[name=UF_CAT3]').append('<option sect="' + data.ID[i] + '" value="' + data.NAME[i] + '">' + data.NAME[i] + '</option>');
	// 					$('#UF_CAT3').show();
	// 				}
	// 			}
	// 		});
	// 	});
	// });
	
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

    $("body").on("change", "#surname_custom, #name_custom, #middle_name_custom, #id11", function (s) {
        let surname = $('#surname_custom').val();
        let name = $('#name_custom').val();
        let middle = $('#middle_name_custom').val();
        let str = `${surname} ${name} ${middle}`;
        let personalMobile = $('#id11').val();
        $('#PERSONAL_MOBILE').val(personalMobile);
        $('#id3').val(str);
    });

    // $("body").on("change", "#category_rus, #category_eng", function (category) {
    //     let category_rus = $('#category_rus').val();
    //     let category_eng = $('#category_eng').val();
    //     let str = `${category_rus} ${category_eng}`;
    //     $('#category').val(str);
    // });

    // Транслитерация

    (function ($) {
        var methods = {
            init: function (options) {
                var o = {
                    eventType:'keyup blur copy paste cut start',
                    elAlias: $(this),				//Элемент, в который будет записываться результат транслитерации или false
                    reg:'',							//'" "="-","ж"="zzzz"' or false or ''
                    translated: function (el, text, eventType) {},
                    caseType: 'inherit',				// lower(default), upper, inherit - регистр выходных данных
                    status:true,
                    string:''						//используется для транслита строковой переменной
                };
                if (options) {
                    $.extend(o, options);
                }
                var general = $(this);
                if(!general.length){
                    general = $('<div>').text(o.string);
                }

                return general.each(function(){

                    var
                        elName = $(this),
                        elAlias = o.elAlias.length ? o.elAlias.css({wordWrap:'break-word'}) : general.css({wordWrap:'break-word'}),
                        nameVal;

                    elName.data({
                        status:o.status
                    })

                    var inser_trans = function(result,e) {

                        if(o.caseType == 'upper'){
                            result = result.toUpperCase();
                        }
                        if(o.caseType == 'lower'){
                            result = result.toLowerCase();
                        }
                        if(elName.data('status') && o.elAlias){
                            if (elAlias.prop("value") !== undefined) {
                                elAlias.val(result);
                            }else{
                                elAlias.html(result);
                            }

                        }
                        if(result != ''){
                            if (o.translated !== undefined) {
                                var type;
                                if(e == undefined){
                                    type = 'no event';
                                }else{
                                    type = e.type;
                                }
                                o.translated(elName, result, type);
                            }
                        }
                    }

                    var customReg = function(str){
                        customArr = o.reg.split(',');
                        for(var i=0;i<customArr.length; i++){
                            var customItem = customArr[i].split('=');
                            var regi = customItem[0].replace(/"/g,'');
                            var newstr = customItem[1].replace(/"/g,'');
                            var re = new RegExp(regi,"ig");
                            str = str.replace(re,newstr)
                        }
                        return str
                    }

                    var tr = function(el,e){
                        if (el.prop("value") !== undefined) {
                            nameVal = el.val();
                        }else{
                            nameVal = el.text();
                        }
                        if(o.reg && o.reg != ''){

                            nameVal = customReg(nameVal)

                        }
                        inser_trans(get_trans(nameVal),e);
                    };
                    elName.on(o.eventType,function (e) {
                        var el = $(this);
                        setTimeout(function(){
                            tr(el,e);
                        },50)
                    });
                    tr(elName);
                    function get_trans() {
                        en_to_ru = {
                            'а': 'a',
                            'б': 'b',
                            'в': 'v',
                            'г': 'g',
                            'д': 'd',
                            'е': 'e',
                            'ё': 'jo',
                            'ж': 'zh',
                            'з': 'z',
                            'и': 'i',
                            'й': 'j',
                            'к': 'k',
                            'л': 'l',
                            'м': 'm',
                            'н': 'n',
                            'о': 'o',
                            'п': 'p',
                            'р': 'r',
                            'с': 's',
                            'т': 't',
                            'у': 'u',
                            'ф': 'f',
                            'х': 'h',
                            'ц': 'c',
                            'ч': 'ch',
                            'ш': 'sh',
                            'щ': 'sch',
                            'ъ': '',
                            'ы': 'y',
                            'ь': '',
                            'э': 'e',
                            'ю': 'ju',
                            'я': 'ja',
                            ' ': '_',
                            'і': 'i',
                            'ї': 'i',
                            'є': 'e',
                            'А': 'A',
                            'Б': 'B',
                            'В': 'V',
                            'Г': 'G',
                            'Д': 'D',
                            'Е': 'E',
                            'Ё': 'Jo',
                            'Ж': 'Zh',
                            'З': 'Z',
                            'И': 'I',
                            'Й': 'J',
                            'К': 'K',
                            'Л': 'L',
                            'М': 'M',
                            'Н': 'N',
                            'О': 'O',
                            'П': 'P',
                            'Р': 'R',
                            'С': 'S',
                            'Т': 'T',
                            'У': 'U',
                            'Ф': 'F',
                            'Х': 'H',
                            'Ц': 'C',
                            'Ч': 'Ch',
                            'Ш': 'Sh',
                            'Щ': 'Sch',
                            'Ъ': '',
                            'Ы': 'Y',
                            'Ь': '',
                            'Э': 'E',
                            'Ю': 'Ju',
                            'Я': 'Ja',
                            ' ': '_',
                            'І': 'I',
                            'Ї': 'I',
                            'Є': 'E',
                            ' ': ' '
                        };


                        nameVal = trim(nameVal);
                        nameVal = nameVal.split("");

                        var trans = new String();

                        for (i = 0; i < nameVal.length; i++) {
                            for (key in en_to_ru) {
                                val = en_to_ru[key];
                                console.log(key)
                                if (key == nameVal[i]) {
                                    trans += val;
                                    break
                                }else if (key == "Є") {
                                    trans += nameVal[i]
                                };
                            };
                        };
                        console.log(trans)
                        return trans;
                    }

                    function trim(string) {
                        //Удаляем пробел в начале строки и ненужные символы
                        string = string.replace(/(^\s+)|'|"|<|>|\!|\||@|#|$|%|^|\^|\$|\\|\/|&|\*|\(\)|\|\/|;|\+|№|,|\?|:|{|}|\[|\]/g, "");
                        return string;
                    };
                })
            },
            disable: function () {
                $(this).data({
                    status:false
                })
            },
            enable: function () {
                $(this).data({
                    status:true
                })
            }
        };
        $.fn.liTranslit = function (method) {
            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, arguments);
            } else {
                $.error('Метод ' + method + ' в jQuery.liTranslit не существует');
            }
        };
    })(jQuery);

    // Конец транслитерации


    // facedetection



    //

</script>
