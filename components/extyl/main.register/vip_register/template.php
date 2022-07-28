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
	$_SESSION['NEW_REG'] = $arResult['VALUES']["USER_ID"];
	header('Location: /');
}
global $USER;
CModule::IncludeModule("iblock");

if (count($arResult["ERRORS"]) > 0){
		foreach ($arResult["ERRORS"] as $key => $error)
			if (intval($key) == 0 && $key !== 0) 
				$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

		ShowError(implode("<br />", $arResult["ERRORS"]));
	}
?>
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
		<?/*<div class="form-box">
<p> Для успешного завершения регистрации и сохранения данных в системе, пожалуйста, убедитесь, что у Вас  имеются нижеприведенные документы, отвечающие требованиям системы:  
			</p>
			<h3>Требования системы:</h3>
			<p>1. Личная фотография (для бейджа) - цветная фотография в формате JPG, изображение лица должно занимать не менее 70% фотографии, размер не более 3 мб.;
			</p><p>2. Электронная почта (для получения логина и пароля в Личный кабинет);
			</p><p>3. Паспортные данные.
			</p><p>После успешного завершения регистрации и утверждения участия Оргкомитетом Форума, Вы получите доступ в Личный кабинет для получения всей необходимой информации по участию в Форуме.
			</p>
		</div>*/?>
		<div class="form-box step1">
			<div class="row">
				<label for="id3">ФИО</label>
				<input type="text" name="UF_FIO" class="text" value="<?=$arResult["VALUES"]['UF_FIO']?>" err="Иванов Иван Иванович" placeholder = "Иванов Иван Иванович" id="id3">
			</div>

			<div class="row">
				<label for="id102">Город/населенный пункт</label>
				<input name="UF_REG_CITY" type="text" class="text" value="<?=$arResult["VALUES"]['UF_BIRTH_CITY']?>" placeholder="Москва" id="id102">
			</div>
			<div class="row">
				<label for="id5">Компания</label>
				<input name="REGISTER[WORK_COMPANY]" type="text" class="text" value="<?=$arResult["VALUES"]['WORK_COMPANY']?>" id="id5" />
			</div>
			<div class="row">
				<label for="id6">Должность</label>
				<input type="text" name="REGISTER[WORK_POSITION]" class="text" value="<?=$arResult["VALUES"]['WORK_POSITION']?>" id="id6">
			</div>
			<div class="row">
				<label for="id10">Электронная почта</label>
				<input name="REGISTER[LOGIN]" type="text" class="text" value="<?=$arResult["VALUES"]['LOGIN']?>" id="id10" err="Ivanov@mail.ru" placeholder="Ivanov@mail.ru">
			</div>
			<div class="row">
				<label>Личное фото:</label>
				<div class="right-column-inline">
					<div class="download-wrap">
						<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
							<div id="upload_photo" class="photo"></div>
						<?endif?>
						<p>Цветная фотография на однородном фоне в формате JPG, размер фотографии не меньше чем 300х400 пикселей, фотография анфас, без головного убора, изображение лица должно занимать не менее 70% фотографии, размер не более 3 МБ.</p>
						<p><a href="https://www.sportdergava.ru/upload/iblock/54e/%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F-%D0%BA-%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%8F%D0%BC.pdf" target="_blank">Требования к фотографии</a></p>
					</div>

					<?if(user_browser($_SERVER['HTTP_USER_AGENT']) != "IE 8.0"):?>
						<input type="hidden"  name="file" value="" />
						<button id="btn-start-camera" class="btn-photo">Сделать фото с веб-камеры</button>
					<?else:?>
						<input type="file" name="REGISTER_FILES_PERSONAL_PHOTO" />
					<?endif?>

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
			<div class="submit-wrap row">
				<input id="register_submit_button" type="submit" name="register_submit_button" class="submit halfwidth" value="Завершить" disabled/>
				<input type="hidden" name="register_submit_button" class="submit" value="Завершить" />
				<div class="btn-note"></div>
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
	
	function isValidEmail (email, strict){
		if ( !strict ) email = email.replace(/^s+|s+$/g, '');
		return (/^([a-z0-9_-]+.)*[a-z0-9_-]+@([a-z0-9][a-z0-9-]*[a-z0-9].)+[a-z]{2,4}$/i).test(email);
	}

	$('#id13').change(function(){
		if($('#register_submit_button').attr('disabled')=='disabled') {
			$('#register_submit_button').attr('disabled', false);
		} else {
			$('#register_submit_button').attr('disabled', 'disabled');
		}
	});

	$('#register_submit_button').click(function(e){
		e.preventDefault();
		$('.step1 .btn-note').html('');
		var err = 0;
		var err_text = 'Вы не заполнили поля:<br/>';

		$('.step1 [type=text]').each(function(i){
			if($(this).val()=='' || $(this).val() == $(this).attr('err')){
				if($(this).attr('name') != "UF_NUM_ACCRED" && $(this).attr('name') != "UF_REG_SECTOR" && $(this).attr('name') != "UF_REAL_SECTOR" && $(this).attr('name') != "UF_REG_CORPUS" && $(this).attr('name') != "UF_REAL_CORPUS"){
					err++;
					err_text += '<a href="#' + $(this).attr('id') + '">' + $(this).parent().find('label').text() + '</a>&nbsp;';
				}
			}
		});
		if($('[name=UF_FIO]').val() != '' && $('[name=UF_FIO]').val() != $('[name=UF_FIO]').attr('err')){
			var reg = /^[а-яА-Я0-9 .,]+$/;
			if(!reg.test($('[name=UF_FIO]').val())){
				err++;
				err_text += '<br><a href="#id3">ФИО - Разрешены только русские буквы</a>';
			}
		}
		if(!isValidEmail($('#id10').val(),true) && $('#id10').val() != ''){
			err++;
			err_text += '<br><a href="#' + $('#id10').attr('id') + '">Не верный формат Email</a>';
		}
		$.post('/ajax/get_email.php', {EMAIL: $('#id10').val()}, function(data){
			if(data=='ok'){
				err++;
				err_text += '<br><a href="#' + $('#id10').attr('id') + '">Данный Email уже существует</a>';
			}
			if(err==0){
				$('.reg-form').submit();
			}
			else{
				$('.step1 .btn-note').html(err_text);
			}
		});
	});
</script>