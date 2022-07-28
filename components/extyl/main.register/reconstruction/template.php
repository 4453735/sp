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
                console.log(file);
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


			<div class="form-box">
				<p>В настоящее время на сайте проводятся технические работы.</p>
			</div>


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

	$('#register_submit_button').click(function(e){
		e.preventDefault();
        if ($('[name=UF_CAT1]:checked').val() == "" || $(this).val() == $(this).attr('err')) {
            err++;
            err_text += '<a href="#UF_CAT1">Категория 1</a>';
        }
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

    $("body").on("change", "#surname_custom, #name_custom, #middle_name_custom", function (s) {
        let surname = $('#surname_custom').val();
        let name = $('#name_custom').val();
        let middle = $('#middle_name_custom').val();
        let str = `${surname} ${name} ${middle}`;
        $('#id3').val(str);
    });
</script>
