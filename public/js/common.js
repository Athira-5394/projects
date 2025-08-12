(function($){


	//ページトップボタン
	$(function() {
	    var topBtn = $('.back_btn');
	    topBtn.hide();
	    //スクロールが100に達したらボタン表示
	    $(window).scroll(function () {
	        if ($(this).scrollTop() > 100) {
	            topBtn.fadeIn("slow");
	        } else {
	            topBtn.fadeOut("fast");
	        }
	    });
	    //スクロールしてトップ
	    topBtn.click(function () {
	        $('body,html').animate({
	            scrollTop: 0
	        }, 500);
	        return false;
	    });
	});

	//スムースリンク
	$(window).on('load', function() {
		$('a.smooth').click(function() {
			var $target=$(this.hash);
			var targetY=$target.offset().top;
			$('html, body').animate({scrollTop: targetY},500);
			return false;
		});
	});


	$(function() {
		if (navigator.userAgent.indexOf('iPhone') > 0 || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0) {
			//回転時にリロード
			$(window).on('orientationchange',function(){
				$(function() {
					location.reload();
				});
			});
		}

		//iPad
		if (navigator.userAgent.indexOf('iPad') > 0) {
			$(function() {
				$("meta[name='viewport']").attr('content', 'width=1110');
			});
		}

		//Android
		if (navigator.userAgent.indexOf('Android') > 0) {
			$(window).on('orientationchange',function(){
				$(function() {
					location.reload();
				});
			});
		}
	});

})(jQuery);
