<?php
/* StuReSy - Student Response System
 * Copyright (C) 2012-2014  StuReSy-Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>  <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>  <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>  <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
 <title>StuReSy</title>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
   
 <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
 <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
 <link rel="apple-touch-icon" sizes="57x57" href="img/apple-touch-icon-57x57.png">
 <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
 <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">
 <link rel="apple-touch-icon" sizes="144x144" href="img/apple-touch-icon-144x144.png">
 
 <style>
	 body {padding-bottom: 60px; }
	 @media (min-width: 981px) { body { padding-top: 60px; } }
	 .form-signin {	max-width: 300px; padding: 19px 29px 29px; margin: 0 auto 20px;	background-color: #f5f5f5;
	 border: 1px solid #e5e5e5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;
	 -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
	 box-shadow: 0 1px 2px rgba(0,0,0,.05);}
	 .form-signin .form-signin-heading, .form-signin .checkbox { margin-bottom: 10px;}
	 table form{ margin-bottom: 0px;}
 </style> 
 <link rel="stylesheet" href="css/bootstrap.min.css">
 <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
 <style>
	 .table th, .table td { border-top: 0px solid; }
	 input[type=checkbox] {display:none;}
	 input[type=checkbox] + label {
        display:border:1px solid black;inline-block;
        margin:-2px;
        padding: 4px 12px;
        margin-bottom: 0;
        font-size: 14px;
        line-height: 20px;
        color: #333;
        text-align: center;
        text-shadow: 0 1px 1px rgba(255,255,255,0.75);
        vertical-align: middle;
        cursor: pointer;
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
        background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
        background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
        background-image: -o-linear-gradient(top,#fff,#e6e6e6);
        background-image: linear-gradient(to bottom,#fff,#e6e6e6);
        background-repeat: repeat-x;
        border: 1px solid #ccc;
        border-color: #e6e6e6 #e6e6e6 #bfbfbf;
        border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
        border-bottom-color: #b3b3b3;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
    }
 
    input[type=checkbox]:checked + label {
        background-image: none;
        outline: 0;
        -webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        background-color:#e0e0e0;}
 </style>

 <script async="true" src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
 <script async="true" src="js/jquery-1.9.0.min.js"></script>
 <script async="true" src="js/bootstrap.min.js"></script> 
 <script type="text/javascript">
	 function reload() {location.reload(true); }
	 function timedRefresh(timeoutPeriod) {setTimeout("window.location.href = window.location.href;",timeoutPeriod); }
 </script>
</head>
