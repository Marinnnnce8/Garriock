<?php namespace ProcessWire;

/**
 * _main.php
 *
 * Please integrate common template elements here
 *
 */

// Set the meta title if not set
$page->metaTitle = $page->metaTitle ?: 
	($page->seo_title ?: 
		($page->isHome ? "$nb->siteName | " . $page->get("strapline|title") : "$page->title | $nb->siteName")
	);

include("./inc/head.php");

?><body class="<?=$page->template->name?>">
<!--[if lte IE 8]>
<div style=clear:both;text-align:center;position:fixed;bottom:0;left:50%;margin-left:-490px;z-index:16180339887;><a
        href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img
        src=https://s3-eu-west-1.amazonaws.com/nb-processwire/update-browser.jpg border=0 height=50 width=980
        alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a>
</div>
<![endif]-->
<div id="site-wrapper">
    <header id="site-header" class='header'>
        <div class="top-line">
            <div class="company-name"><strong><?=$nb->clientName?></strong> Main Company Site</div>
            <div class="uk-text-center middle">
                <a href="<?= $urlEdinburgh ?>" rel="noopener noreferrer" target="_blank" class="uk-button uk-button-link">
                    <strong><?=$pageHome->links[0]->title?></strong><i class="fas fa-external-link-alt"></i></a></div>

            <div class="company-contact">
                <a href="tel:<?= nb()->formatTelHref($nb->clientTel)?>" class="phone"><i class="fas fa-phone fa-flip-horizontal"></i><span>LERWICK</span><span class="uk-visible@m">OFFICE:</span> <?=$nb->clientTel?></a>
                <a href="<?=$urlEnquire?>" class="enquiry-button uk-button uk-button-primary uk-button-small pull-icon-right">ENQUIRE<i class="fas fa-long-arrow-alt-right"></i></a>
            </div>
        </div>
        <div class="uk-container uk-container-expand">
            <nav class='uk-navbar'>
                <div class="uk-navbar-left">
                    <a href="<?=$pageHome->url?>" class="uk-navbar-item uk-logo">
                        <img src="<?=$urls->templates?>img/logo.png" alt='<?= $nb->siteName ?> Logo'>
                    </a>
                    <div class="okayNav">
                        <ul class='navbar-main uk-navbar-nav'>
                            <?= renderNavItems($nb->navItems, false, false) ?>
                            <li class="uk-hidden@s">
                                <a href="<?= $urlEdinburgh ?>" target="_blank" class="ext-link">
                                    <?=$pageHome->links[0]->title?>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="uk-navbar-right uk-visible@s">
                    <div class="uk-nav">
                        <a href="<?= $nb->clientData("Social")['Facebook'] ?>" class="font-size-18 uk-button uk-button-primary uk-button-small" rel="noopener noreferrer" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?=$urlEnquire?>" class="uk-button uk-button-primary uk-button-small pull-icon-right">ENQUIRE NOW<i class="fas fa-long-arrow-alt-right"></i></a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

	<?= $prepend ?>
        
<?php if($page->wrapContent): ?>
    <section id="wrapper-content" class="wrapper-content">
	<div class="hero-section full-screen">
            <div class="hero-inner hero-gradient" data-src="<?= $page->imageBanner->url?>" data-srcset="<?=$page->imageBanner->url?> 1024w, <?=$page->imageBanner->url?> 1920w" data-sizes="100vw" 
                 data-uk-img="">
                <div class="uk-container">
                    <div class="vertical-center">
                        <div class="hero-content">
                            <h1 class="uk-heading-hero uk-light underlined"><?=$page->titlePage?></h1>
                            <?= renderBreadCrumbs($page)?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="page-content">
        <?php if (!in_array($page->template->name, ['study', 'branch', 'service'])): ?>
            <div class="uk-container lift-me-up uk-background-default">
                <div class="main-grid">
                    <div class="content uk-margin-large-bottom">
                        
                            <?= $before ?>
                            <?= nbIntro($page) ?>
                        
                    </div><!-- .content -->         
                    <div id='page-<?= $page->template->name ?>'>
                            <?= $content ?>
                    </div>                    
                </div>
            </div>
        <?php else: ?>
            <?= $before ?>
            <div class="main-grid">
                <?=$content?>
            </div>
        <?php endif; ?>
            <?= $after ?>
        </div>
        
    </section>
<?php else: ?>
	<div id='page-<?= $page->template->name ?>'>
            <?=$content?>
        </div>
<?php endif; ?>
         
	<?= $append ?>

        <div class="uk-section uk-padding-xsmall">
            <div class="uk-grid-xsmall uk-child-width-1-2@s uk-child-width-1-4@l" data-uk-grid="">
                <?php foreach ($pageHome->links as $link): 
                        echo renderHomeLink($link);
                endforeach; ?>
            </div>
        </div>

	<footer id="site-footer" 
                data-src="<?=$urls->template?>img/footer-bg-mobile.jpg" 
                data-srcset="<?=$urls->template?>img/footer-bg-mobile.jpg 650w, <?=$urls->template?>img/footer-bg.jpg 1024w" 
                data-sizes="100vw" data-uk-img="">
            <div class="uk-container">

                <div class="footer-sidebar sidebar">
                    <div class="uk-child-width-1-4@l uk-grid-large uk-child-width-1-2@s" data-uk-grid="">

                        <div>
                            <div class="widget widget-get-in-touch">
                                <h4 class="widget-title">Main Office Contact</h4>

                                <p>
                                    <?=
                                implode("<br>", [
                                "<strong>$nb->clientName</strong>",
                                nl2br($nb->clientAddress),
                                "T: " . nbTel($nb->clientTel),
                                "F: " . nbTel($nb->clientData("Tels")["Fax"]),
                                "E: " . nbMailto($nb->clientEmail),
                            ]);
                            ?>
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="widget">
                                <h4 class="widget-title">Services</h4>
                                <?=
                                $nb->htmlWrap(
                                        $pages->find("template=service,limit=7")->each($tplLink), "<ul class='uk-list'>"
                                )
                                ?>
                            </div>
                        </div>

                        <div>
                            <div class="widget">
                                <h4 class="widget-title">Company</h4>
                                <?=
                                $nb->htmlWrap(
                                        $pages->get(1065)->children()->each($tplLink) .
                                        $pages->find("template=branch")->each($tplLink), "<ul class='uk-list'>"
                                )
                                ?>
                            </div>
                        </div>

                        <div>
                            <div class="widget widget-quick-links">
                                <h4 class="widget-title">Quick Links</h4>
                                <ul class="uk-list">
                                    <li><a href="<?= $pages->get(1075)->url ?>" class="uk-button uk-button-semiwhite ghost pull-icon-right">Current Vacancies<i class="fas fa-long-arrow-alt-right"></i></a></li>
                                    <li><a href="<?= $pages->get(1467)->url ?>" class="uk-button uk-button-semiwhite ghost pull-icon-right">Locate Nearest Office<i class="fas fa-long-arrow-alt-right"></i></a></li>
                                    <li><a href="<?= $urlEnquire ?>" class="uk-button uk-button-semiwhite ghost pull-icon-right">Enquiry form<i class="fas fa-long-arrow-alt-right"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="footer-bottom-line uk-background-default uk-child-width-1-3@m">
                <div>
                    <ul class="secondary-navbar">
                        <?= $pages("template=sitemap|legal, include=hidden")->each("<li><a href={url}>{title}</a></li>") ?>
                    </ul>

                    <small class="copyright"><?= nbCopyright() ?></small>
                    <?= nbWatermark() ?>
                </div>

                <div>
                    <div class="partners">
                        <?php if ($pageHome->partners_links->count): ?>
                            <?php foreach ($pageHome->partners_links as $partner): ?>
                        <a href="<?= $partner->ad_link ?>" class="partner-link" aria-label="<?= $partner->title ?>">
                            <img src="<?= $partner->thumb->url ?>" alt="<?= $partner->title ?>">
                        </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php if ($modules->MarkupSocialShareButtons): ?>
    <?= renderFloatingShareButtons($modules->MarkupSocialShareButtons) ?>
    <?php endif; ?>
    
    <div id="totop" class="uk-hidden@l uk-position-small uk-position-bottom-right uk-position-fixed gototop uk-position-z-index">
        <div class="uk-card uk-card-secondary uk-box-shadow-hover-small uk-card-body uk-padding-xsmall uk-border-rounded">
            <a href="#" data-uk-totop data-uk-scroll> </a>
        </div>
    </div>  
    
<?php if ($pageHome != $page): ?>
</div>
<?php endif; ?>
    <?php
	include("./inc/foot.php");

// render submenus items
        
	?>
    <div class="uk-position-absolute uk-position-bottom main-navigation-dropdowns">
<?php foreach ($nb->navItems as $subMenu): 
    $hasChildren = $subMenu->children->count && !in_array($subMenu->template->name, [
			"home",
			"posts",
			"studies",
		]);
    if ($hasChildren): ?>
        <div data-toggler="#submenu-<?= $subMenu->id ?>" class="uk-navbar-dropdown drop">
            <ul class="uk-nav uk-navbar-dropdown-nav">
                <li class="dropdown-header">
                    <a href="<?= $subMenu->url ?>" class="uk-text-uppercase">
                        <?= $subMenu->title ?>
                    </a>
                </li>
            <?php foreach ($subMenu->children() as $child): ?>    
                <li><a href="<?= $child->url ?>"><?= $child->title ?></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif;
endforeach; ?>
    </div>
</body>
</html>
