.cms-section-add{
	border:1px solid #EEE;
	padding:16px;
	width:auto;
	text-align:center;
}
.cms-section-add a{
	text-align:center;
	font-weight:bold;
}


.cms-section{
	display:block;
	min-height: 425px;
	position:relative;
	overflow:hidden;
}
.cms-section .container{
	margin:0 auto;
	min-width: 67.14285714285714em;
	max-width: 82.14285714285714em;
	box-sizing: border-box;
	display:block;
	line-height: 420px;
}

.cms-section .container .left{
	float:left;
	max-width:500px;
}

.cms-section .container .right{
	float:right;
	max-width:500px;
}
.cms-section .container .cell{
	display:inline-block;
	vertical-align:middle;
}
.cms-section .container a{
	color:#000;
}


.cms-section-bg{
	
	background-size: cover;
	background-position: 60% center;
	background-repeat: no-repeat;
	
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 610px;
	margin-top: -40px;
	z-index: -1;
	/*-webkit-transform: translate3d(0, -20px, 0);
	transform: translate3d(0, -20px, 0);
	*/
}
.cms-section .container .cell h2{
	font-family:"Lato", Helvetica, Arial, Sans-Serif;
	font-size:28px;
	font-weight:normal;
}
.cms-section .container .cell p{
	font-family:"Lato", Helvetica, Arial, Sans-Serif;
	font-size:16px;
}

.cms-section .container .cell input.h2{
	font-family:"Lato", Helvetica, Arial, Sans-Serif;
	font-size:28px;
	font-weight:normal;
	border:0;
	background:transparent;
}
.cms-section .container .cell textarea.p{
	font-family:"Lato", Helvetica, Arial, Sans-Serif;
	font-size:16px;
	border:0;
	background:transparent;
	width:500px;
}


.cms-section-admin{
	display: table;
	position: absolute;
	right: 0;
	padding: 16px;
}
.cms-section-admin .cms-icon{
	border:1px solid #DDD;
	background:#EEE;
	padding:8px;
	font-weight:bold;
	float:left;
	clear:both;
	line-height:14px;
	width:90px;
}
.icon-bg{
	position:relative;
}
.icon-bg input{
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	padding: 0;
	font-size: 20px;
	cursor: pointer;
	opacity: 0;
	filter: alpha(opacity=0);
}


/**
 * Footer
 */
.cms-footer{
	background: #222;
	width: 100%;
	padding: 64px 0;
	height: 110px;
	display: block;
	position: relative;
}
.cms-footer > .inner{
	width:900px;
	display:block;
	margin:auto;
}
.cms-footer > .inner .cms-footer-nav{
	float:left;
}
.cms-footer-nav ul li a{
 	font-size:14px;
 	font-weight:100;
 	font-family:"Lato", Helvetica, Arial, Sans-serif;
 	padding:8px;
 	color:#EEE;
 }
 	
.cms-footer-copyright{
	float:right;
	clear:left;
	color:#888;
	font-size:12px;
	font-family:"Lato", Helvetica, Arial, Sans-serif;
}
.cms-footer-social{
	float:right;
}
.cms-footer-social a{
	color:#BBB;
	font-size:34px;
	padding:0 4px;
}


/**
 * Pages
 */

.cms-page-body .hero{
	margin-bottom:-230px;	
}
.cms-page-body p, .cms-page-body h2{
	font-family:"Lato", Helvetica, Arial;
}
.cms-page-body h2{
	line-height:90px;
}
.cms-page-body p{
	font-weight:300;
	font-size:16px;
}
.cms-sidebar-wrapper{
	width: 164px;
	margin-right: 32px;
	padding-right: 32px;
	border-right: 1px solid #EEE;
}

.cms-pages-sidebar{

}
.cms-pages-sidebar ul{
	float:none;
	text-align:left;
}
.cms-pages-sidebar ul li{
	float:none;
	display:block;
}
.cms-pages-sidebar ul li a{
	display:block;
	font-size:13px;
	font-weight:bold;
	color:#333;
	padding:16px 32px;
	border-bottom:1px solid #DDD;
}
.cms-pages-sidebar ul li a:hover{
	background:#EEE;
	cursor:pointer;
}
.cms-pages-sidebar-admin{
	margin-top:32px;
}
.cms-pages-sidebar-admin > a{
	background:#EEE;
	padding:16px 32px;
	border-bottom:1px solid #DDD;
	color:#333;
	font-weight:bold;
	display:block;
}
.elgg-form-cms-page h4{
	font-size:16px;
	font-weight:bold;
	line-height:18px;
}
.elgg-form-cms-page p{
	padding:8px 0;
}
