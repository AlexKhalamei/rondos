// First we get the viewport height and we multiple it by 1% to get a value for a vh unit
let vh = window.innerHeight * 0.01;
// Then we set the value in the --vh custom property to the root of the document
document.documentElement.style.setProperty('--vh', `${vh}px`);

// We listen to the resize event
window.addEventListener('resize', () => {
  // We execute the same script as before
  let vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
});

$(window).on('load', function () {
    var $preloader = $('.page_loader'),
        $spinner   = $preloader.find('.cssload-loading');
    $spinner.fadeOut();
    $preloader.delay(350).fadeOut('slow');
});

UIkit.util.on('#js-scroll-trigger', 'scrolled', function () {
	UIkit.offcanvas("#offcanvas-push").hide();
});

$(document).on('submit', '.form', function (e) {
    $.post(
        "saver.php",
        {
            name: $(this).find('.firstName').val(),
            email: $(this).find('.Email').val()
        }
    );
    return false;
});

$('.contact-form').submit(function() {

	var a = $(this);
	var name =  a.find('[name = name]').val();
	var email =  a.find('[name = email]').val();
	
	if(window.location.search){
		var utm =  window.location.search;
	}
	var error_name = 0;
	var error_email = 0;

	$('.popup-panel-text').html('');

	if (name.length < 2) {
		error_name = 1;
	}

	if (email.length < 4) {
		error_email = 1;
	}
	
	a.find('[name]').removeClass('tm-form-danger');

	if (error_name) {
		a.find('[name = name]').addClass('tm-form-danger');
	}

	if (error_email) {
		a.find('[name = email]').addClass('tm-form-danger');
	}
	

	if (!error_name && !error_email) {
		$.ajax({
			type: "POST",
			url: "/saver.php",
			data: {	 name: name,
					 email: email,
					 utm: utm},
			beforeSend: function() {
				var button_text = a.find('[type=submit]').html();
				a.find('[type=submit]').prop("disabled", true);
				a.find('[type=submit]').html("<div uk-spinner=''></div>");
				a.find('[type=submit]').attr("data-text", button_text);
			},
			success: function(json) {
				if(json['success']) {
					$('.confirm__text').html("<p>Заявку вiдправлено.</p><p>Для отримання пропозиції будь ласка перейдіть у свій email і підтвердіть його!</p>");
					UIkit.modal('#modal-confirm').show();
					a.find('[type = text]').val('');
					/*
					setTimeout(function() {
					  UIkit.modal('#popup').hide();
					}, 3000);
					*/
				} else {
					$('.confirm__text').html('<p>Не так сталось як гадалось. Ви офлайн або в нас щось зламалось :(((</p><p> Зателефонуйте нам +38&nbsp;(050)&nbsp;980&nbsp;82&nbsp;20</p>');
					// console.log();
					UIkit.modal('#modal-confirm').show();
				}
			},
			complete: function() {
				a.find('[type=submit]').html(a.find('[type=submit]').attr("data-text"));
				a.find('[type=submit]').prop("disabled", false);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				a.find('[type=submit]').html(a.find('[type=submit]').attr("data-text"));
				$('.confirm__text').html('<p>Ви офлайн або в нас щось зламалось :(((</p><p> Зателефонуйте нам +38&nbsp;(050)&nbsp;980&nbsp;82&nbsp;20</p>');
				UIkit.modal('#modal-confirm').show();
				// alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	return false;
});