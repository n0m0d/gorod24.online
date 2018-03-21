	</main>

	<footer class="main-footer">
		<div class="container-wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="content">
							<h2>Как скачать без Play Market</h2>
							<ul>
								<li>1. Зайдите на эту страницу с мобильного телефона.</li>
								<li>2. Нажмите кнопку скачать</li>
								<li>3. Выберите полученный файл на устройстве (нажмите на него)</li>
								<li>4. В настройках разрешите "Не известные источники"</li>
								<li>5. Нажмите кнопу установить. Начнется установка.</li>
								<li>6. Все готово можете пользоваться приложением</li>
							</ul>
							<h3>Мы планируем постоянное обновление</h3>
							<p class="desc-text">Для обновления нужно провести туже самую операцию</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="main-player-big-volume-button">
			<span class="on">
				<i class="fa fa-volume-up on" aria-hidden="true"></i>
				<i class="fa fa-volume-off off" aria-hidden="true"></i>
			</span>
		</div>
		
		<audio id="mp3" src="/uploads/audio/mp3_5a2f7b42615c6.mp3" autoplay>
		<script>
		var mp3 = document.getElementById("mp3");
		mp3.volume = 0.2;
		$(function(){
			
			$('.fa-volume-off').click(function(){
				$(this).parent().removeClass('off').addClass('on');
				var mp3 = document.getElementById("mp3");
				mp3.play();
			});
			
			$('.fa-volume-up').click(function(){
				$(this).parent().removeClass('on').addClass('off');
				var mp3 = document.getElementById("mp3");
				mp3.pause();
			});
			
		});
		
		</script>
	</footer>
	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter47005905 = new Ya.Metrika({
                    id:47005905,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
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
<noscript><div><img src="https://mc.yandex.ru/watch/47005905" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
