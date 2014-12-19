<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Chinelos Battle</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="include/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="include/js/jquery.v1.4.2.js"></script>
<script type="text/javascript" src="include/js/jquery.cycle.all.min.js"></script>
<script type="text/javascript" src="include/js/custom.js"></script>
<!--  ACTIVATE CUFON TEXT REPLACEMENT IF ENABLED IN THEME OPTIONS  -->
<script type="text/javascript" src="include/js/cufon-yui.js"></script>
<script type="text/javascript" src="include/js/liberation_sans.js"></script>
<script type="text/javascript">
	Cufon.replace('h1, h2, h3, h4, h5, h6', { hover: true });
</script>
</head>
<body>
<div class="main">
  <div class="header">
    <div class="block_header">
      <div class="logo"><a href="index.php?module=connexion&action=connexion"><img src="include/images/logo.png" width="175" height="97" border="0" alt="logo" /></a></div>
      <div class="rss"><a href="#"><img src="include/images/rss_1.gif" alt="rss" border="0" /></a> <a href="#"><img src="include/images/rss_2.gif" alt="rss" border="0" /></a> <a href="#"><img src="include/images/rss_3.gif" alt="rss" border="0" /></a> <a href="#"><img src="include/images/rss_4.gif" alt="rss" border="0" /></a> <a href="#"><img src="include/images/rss_5.gif" alt="rss" border="0" /></a> Nous contacter : <br />
        <br />
        Pour plus d'informations : <a href="#"> Ba.. tant pis </a></div>
		<div class="clr" >
			<div class="search" >
				<form id="form1" name="form1" method="GET" action="http://www.google.com/custom" >
					<label ><span >
					<input name="q" type="text" class="keywords" id="textfield" maxlength="50" value="" />
					</span >
						<input name="b" type="image" src="include/images/search.gif" class="button" />
					</label >

				</form >
				<div id="bouton_connexion"> <?php
					require_once 'modules/mod_connexion/vue/vue_connexion.php';
					ModConnexionVueConnexion::afficherBouton();
					?>
				</div>
			</div >
		</div>
      <div class="menu">
        <ul>
          <li><a href="index.php?module=connexion&action=connexion" class="active">Accueil<br />
            <span>connexion</span></a></li>
          <li><a href="index.php?module=combat">Combat<br />
            <span>combattre</span></a></li>
          <li><a href="index.php?module=boutique">Boutique<br />
            <span>shopping</span></a></li>
          <li><a href="index.php?module=joueur">Joueur<br />
            <span>équipes</span></a></li>
          <li><a href="index.php?module=contact">Contact<br />
            <span>assistance</span></a></li>

        </ul>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  <div class="modulesJeu">
		<?php
			include ( "modules" . DIR_SEP . "mod_$module" . DIR_SEP . "mod_$module.php" );
		?>
	</div>
	<?php 
		if(!isset($_GET['module'])){
			$_GET['module']=NULL;
		}
		if($_GET['module']=='connexion' OR $_GET['module']==NULL){ ?> 
  <div class="accord_top">
    <div id="feature_wrap">
      <div id="featured">
        <div class="featured featured1"><a href="#"><img id="imgContent" src="include/images/content_12.jpg" alt="screen 1" /></a></div>
        <div class="featured featured2"><a href="#"><img id="imgContent" src="include/images/content_13.jpg" alt="screen 2" /></a></div>
        <div class="featured featured3"><a href="#"><img id="imgContent" src="include/images/content_14.jpg" alt="screen 3" /></a></div>
        <div class="featured featured4"><a href="#"><img id="imgContent" src="include/images/content_15.jpg" alt="screen 4" /></a></div>
        <div class="featured featured5"><a href="#"><img id="imgContent" src="include/images/content_16.jpeg" alt="screen 5" /></a></div>
      </div>
    </div>
  </div>
  <div class="blog_body_resize">
    <div class="blog_body">

    </div>
  </div>

  <div class="clr"></div>
  <div class="body">
    <div class="body_resize">
      <div class="right">
        <h2>News & Events<br />
        <span> News et Events du jeu en direct</span></h2>
        <img id="imgNews" src="include/images/majbeta13.jpg" alt="img" class="floated" />
        <p> <a href="#">30 Nov 2014</a><br />
          <span>MAJ Beta 1.3</span><br />
          Mise à jour de différentes fonctionnalités comme les combats, les joueurs ou encore les équipes.</p>
        <div class="bg"></div>
        <img id="imgNews" src="include/images/imgreunion.jpg" alt="img" class="floated" />
        <p> <a href="#">22 Nov 2014</a><br />
          <span>Réunion d'équipe</span><br />
         Nous avons fait une réunion d'équipe pour améliorer le jeu. De bonnes idées en sont ressorties. </p>
        <div class="bg"></div>
        <img id="imgNews" src="include/images/imgrobot.jpg" alt="img" class="floated" />
        <p> <a href="#">18 Nov 2014</a><br />
          <span>Système auto</span><br />
          Nous venons de créer un système automatique pour la création des équipes. </p>
        <div class="bg"></div>
        <p><a href="#"><strong>Plus d'informations »</strong></a></p>
      </div>
      <div class="left">
        <h2>Bienvenue sur notre site !<br />
          <span>Chinelos Battle est un mini jeu de combat simple et en multijoueur</span></h2>
        <p>Vous devez vous inscrire pour accéder à notre mini-jeu. Notre site repasse l'actualité l'actualité des jeux vidéos pour vous informer des dernières sorties.</p>
        <p>Nous vous communiquerons les changements qui nous semble nécessaire pour le jeu via les News & Events que vous retrouverez sur votre droite. Vous pouvez aussi accédez aux jeux que nous vous suggerons ci-dessous.. </p>
        <p>&nbsp;</p>
        <h2>Jeux associes<br />
        <span> Petite selection de jeu en ligne pour passer un bon moment </span></h2>
        <a href="http://www.nicoland.fr/"><img id="imgJeu" src="include/images/img_11.jpg" alt="img" class="floated" /></a> <a href="http://fr.grepolis.com/"><img id="imgJeu" src="include/images/img_12.jpg" alt="img" class="floated" /></a> <a href="http://www.swtor.com/fr"><img id="imgJeu" src="include/images/img_13.jpg" alt="img" class="floated" /></a> </div>
      <div class="clr"></div>
    </div>
  </div>
</div>
<?php } ?>

</body>
</html>