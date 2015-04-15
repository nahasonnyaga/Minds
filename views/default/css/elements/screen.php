<?php
/**
 * Multiple screen layout
 */
if(0){ ?><style><?php } ?>

@media all 
and (min-width : 0px)
and (max-width : 1260px) {
		
	.minds-topbar-icon{
		display:none;
	}

}

@media all
and (min-width : 0px)
and (max-width : 1200px) {

	 .minds-button-launch{
                display:none;
        }
	.elgg-menu-topbar{
		display:none;
	}
}

@media all 
and (min-width : 0px)
and (max-width : 720px) {

	.content-carousel .layout{
		margin-top:40px !important;
	}

	.carousel-inner > .item > img{
		top:0 !important;
	}

    .carousel-inner > .item > .carousel-caption p{
        font-size:15px;
        line-height:15px;
    }

    .cms-sections{
        display:none;
    }
	
	.responsive-ad{
		display:block !important;
		padding:16px;
	}

	.elgg-layout-two-sidebar .elgg-sidebar{
		display:none;
	}

	.newsfeed .elgg-main{
		clear:both;
		padding:0;
	}
	.elgg-sidebar-alt.minds-fixed-sidebar-left{
		display:none;
	}

	.elgg-form-activity-post{
		width:auto;
	}
	.upload-progress{
		display:none;
	}

	.minds-fixed-sidebar-left .minds-fixed-avatar{
		margin:16px 0 0;
	}

	.minds-fixed-sidebar-left > a.avatar-edit{
		display:none;
	}

	.sidebar-active-default{
		margin-left:0 !important;
	}
	.sidebar-active-default .topbar{
		width:100% !important;
	}
	.show-default{
		display:none !important;
	}

	.minds-live-chat{
		display:none;
	}

	.elgg-list > li:hover .excerpt, .elgg-list > li:hover .elgg-menu{
		display:none;
	}

	.hero, .elgg-page-default{
		min-width:320px;
	}
	
	.hero > .topbar{
		min-width:320px;
	}

    .hero > .topbar > .inner{
        padding:0;
        width:100%;
    }

	.hero > .topbar > .inner .global-menu{
		margin-top:14px;
	}
	
	.hero > .topbar .logo img.minds-com{
		width: 100%;
		height: auto;
		max-width: 100px;
	}
    
    .hero > .topbar .right .minds-button-register{
        display:none;
    }
	
    .hero > .topbar .right .elgg-button{
		margin: 8px;
		/* width: 52px; */
		font-size: 60%;
		padding: 6px 5px;
		width: auto;
		font-weight: bold;
	}
	
	.hero > .body, .elgg-page-body {	
		margin-top:48px;
        padding-bottom:0;
	}	
	
	.minds-body-header > .inner > .elgg-head{
		min-width:0;
	}

	.hero > .body > .inner, .elgg-page-default .elgg-page-body > .elgg-inner{
		width:100%;
	}

	.hero > .topbar .logo {
		height:22px;
	    padding:8px;
    }
	
	.hero > .topbar .logo h1{
		font-size:14px;
	}
	.logo .tip-logo{
		display:none;
	}

	.hero > .topbar > .inner .menu-toggle {
		margin:0;
        padding:16px;
	}

	.hero > .topbar .search {
		display:none;
	}

	.hero > .topbar .owner_block {
		margin-top:0;
		display:none;
	}

	.hero > .topbar .owner_block > a > img {
		padding:0;
	}

	.hero > .topbar .actions {
		margin: 12px 0;
        width:80px;
	}
    .hero > .topbar .actions .gatherings {
        display:none;
    }

	.hero > .topbar .owner_block > a > .text{
		display:none;
	}

	.content-carousel{
		margin-top:80px;
	}

	.minds-fixed-sidebar-left{
		height:auto;
		float:none;
		box-shadow: 0 0 0;
	}

    .minds-body-header .elgg-menu-title{
        float: none;
        position: relative;
        top: 0;
        right: 0;
        margin: 16px 0;
    }

	.homepage{
		padding-top:64px;
	}

	.heading-main, .elgg-heading-main {
		font-size:24px;
	}

	.carousel, .carousel .item{
		height:240px;
	}
	.carousel-inner > .item > .carousel-caption{
		top:32px;
		left:0px;
	}
	
	.carousel-inner > .item > .carousel-caption h3{
		font-size:24px;
		line-height:24px;
	}
	
	.carousel-fat .minds-body-header{
		height:400px;
	}
	.carousel-fat .carousel{
		height:400px;
	}
	.carousel-fat .carousel .item{
		height:500px;
	}
	.carousel-fat .carousel .item > .carousel-caption{
		top:  100px;
	}
	.carousel-fat .carousel-inner > .item > img {
		position: absolute;
		top: 0;
		left: 0;
		min-width:100%;
		height: 100%;
	}
	
	.carousel-fat .elgg-layout{
		min-height:0;
	}
	.carousel-fat .minds-body-header{
		margin:0;
		padding:0;
	}
	
	.donations-box{
		position:absolute;
		margin-left:0;
		top:200px;
		left:0;
		width:100%;
	}
	.donations-button{
		font-size:11px;
		float:left;
	}

	.frontpage-signup, .front-page-buttons{
		display:none;
	}
	
	/**
	 * 	Hide search for now
	 */
	.search{
		display:none;
	}
	
	/**
	 * 	General fixed widths
	 */
	.elgg-form-login{
		width:70%;
	}

	.elgg-list{
		width:100% !important;
	}
	
	/**
	 * 	Listings and tiles
	 */
	.elgg-list.mason > li{
		width:auto;
		float:none;
	}
	
	/**
	 * 	Pages
	 */
	.sidebar{
	/*	display:none; */
        width:100%;
        float:none;
	}

	.elgg-footer{
		margin-right:0;
	}
   
    .node-select{
        display:none;
    }
	
}
