<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo isset($this->pageTitle) ? $this->pageTitle : Yii::app()->name; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/normalize.css">
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/jquery.rs.carousel.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/jquery.rs.carousel-touch.css" media="all" />

	<!-- lib -->
<!--	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/jquery-1.8.2.min.js"></script>-->
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/jquery.ui.widget.js"></script>
	
	<!-- carousel -->
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/jquery.rs.carousel.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/jquery.rs.carousel-autoscroll.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/jquery.rs.carousel-continuous.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/vendor/modernizr-2.6.2.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans+Caption:700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <!--<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>-->
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/flexible-columns.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 7]>
    <link href="css/core/iehacks.css" rel="stylesheet" type="text/css"/>
    <![endif]-->

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript">
        WebFontConfig = {
            google: { families: [ 'PT Sans Caption' ] },
            custom: { families: ['Arial Narrow'],
                urls: [ '/css/fonts/arial-narrow.css' ] }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                    '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
        $(document).ready(function () {
            $('.rs-carousel').carousel({itemsPerPage:'1', nextPrevLinks:false});
	        if($(':rs-carousel').carousel('getNoOfItems') <= 3){
		        $('.rs-carousel-action-prev').hide();
		        $('.rs-carousel-action-next').hide();
	        }
        });
    </script>
</head>
<body>
<noindex>
    <ul class="ym-skiplinks">
        <li><a class="ym-skip" href="#nav">К навигации</a></li>
        <li><a class="ym-skip" href="#main">К содержимому</a></li>
    </ul>
</noindex>
<div class="ym-wrapper">
    <div class="ym-wbox">
        <header class="header ym-clearfix">
            <a href="<?php echo Yii::app()->homeUrl; ?>"><h1>Электронный научный журнал: Программные продукты, системы и алгоритмы</h1></a>

            <form class="ym-searchform" method="post" action="<?php echo Yii::app()->createAbsoluteUrl('author/article/search'); ?>">
                <input class="ym-searchfield" name="query" type="search"/>
                <button type="submit" class="searchbutton"></button>
            </form>

            <div class="block_add-article">
                <div class="icon icon_add-article"></div>
                <a href="<?php echo Yii::app()->createUrl('author/article/create');?>" class="link text_add-article">Добавить статью</a><br>
	            <a href="<?= Yii::app()->createUrl('author/article/favorite') ?>" class="btn"><i class="icon-heart"></i></a>
	            <?php if (Yii::app()->user->isGuest): ?>
	                <a href="<?php echo Yii::app()->createUrl('user/login'); ?>" class="btn">Вход</a>
	                <a href="<?php echo Yii::app()->createUrl('user/registration'); ?>" class="btn">Регистрация</a>
                <?php elseif(Yii::app()->user->isAdmin()):  ?>
	                <a href="<?php echo Yii::app()->baseUrl.'admin/default'; ?>">Админка</a>
                <?php elseif(!Yii::app()->user->isGuest): ?>
	                <a href="<?php echo Yii::app()->createUrl('author/article'); ?>" class="btn">Личный Кабинет</a>
                <?php endif; ?>
            </div>
        </header>
        <nav id="nav" class="ym-clearfix">
            <div class="ym-hlist">
                <div class="sublogo"></div>
                <div class="tail-corner-top-left"></div>
                <div class="tail-corner-bottom-right"></div>
               <!-- <ul>
                    <li class="active"><strong>Новости</strong></li>
                    <li><a href="#">Проекты и анонсы</a></li>
                    <li><a href="#">Авторам</a></li>
                    <li><a href="#">Редакция</a></li>
                    <li><a href="#">Обратная связь</a></li>
                </ul>-->
                
                <?php $this->widget('application.widgets.MenuWidget', array(
                        'items'=>array(),
                        'activeCssClass' => 'active',
                        'activateItems' => true,
                        'name' => 'Name',
	                    'htmlOptions' => array('class' => 'vert-nav'),
                ));?>
                <div class="placeholder_after-mainmenu"><a href="http://feeds.feedburner.com/cps/swsys-web" class="icon icon_rss">rss</a></div>
            </div>
        </nav>
        <?php echo $content ?>
        <div class="ym-clearfix"></div>
        <footer class="footer ym-clearfix">
            <div class="copyright">Создание сайта: <a href="http://cps.tver.ru" target="_blank" class="link">ЗАО НИИ ЦПС</a></div>
            <div class="footer-content">
                <p>Журнал зарегистрирован в комитете РФ по печати
                    Свидетельство о регистрации средства массовой информации № 013831 от 26.11.1995 г.
                    Свидетельство о регистрации электронного средства массовой информации № ФС77-43342 от 29.12.2010 г.
                    Решение Президиума Высшей аттестационной комиссии Министерства образования и науки РФ № 8/1
                    (о внесении в Перечень ведущих рецензируемых научных журналов и изданий, в которых должны быть
                    основные научные результаты диссертаций на соискание ученых степеней кандидата и доктора наук).
                </p>

                <p>
                    © Все права на авторские материалы охраняются в соответствии с законодательством РФ. Перепечатка
                    возможна только с разрешения редакции. При цитировании материалов обязательна ссылка на
                    Международный
                    журнал "Программные продукты и системы" (для on-line проектов обязательна гиперссылка).</p>
            </div>
        </footer>
    </div>
</div>


<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
<!--<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.2.min.js"><\/script>')</script>-->
<!--<script src="js/plugins.js"></script>-->
<!--<script src="js/main.js"></script>-->

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    var _gaq = [
        ['_setAccount', 'UA-XXXXX-X'],
        ['_trackPageview']
    ];
    (function (d, t) {
        var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
        g.src = ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g, s)
    }(document, 'script'));
</script>
<script type="text/javascript">
	$(document).ready(function () {

		$('.vert-nav li').hover(
			function() {
				$('ul', this).slideDown(110);
			},
			function() {
				$('ul', this).slideUp(110);
			}
		);

	});
</script>
</body>
</html>
