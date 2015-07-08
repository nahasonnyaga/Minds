<?php
/**
 * Minds frontend
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');
error_reporting(E_ALL); 
ini_set( 'display_errors','1');

?>
<html>
  <head>
    <title>Minds <?= "" ?></title>
    <base href="/">
    
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:400,700'>
    <link rel="stylesheet" href="stylesheets/main.css"/>
  </head>
  <body>

  
    <!-- The app component created in app.ts -->
    <minds-app>Loading...</minds-app>
    
     <!-- inject:js -->
  	 <script src="/lib/traceur-runtime.js?v=0.0.1"></script>
  	 <script src="/lib/es6-module-loader-sans-promises.js?v=0.0.1"></script>
  	 <script src="/lib/Reflect.js?v=0.0.1"></script>
  	 <script src="/lib/system.src.js?v=0.0.1"></script>
  	 <script src="/lib/zone.js?v=0.0.1"></script>
  	 <script src="/lib/angular2.js?v=0.0.1"></script>
  	 <script src="/lib/router.js?v=0.0.1"></script>
  	 <!-- endinject -->
    
    <script>
        System.config({
          baseURL: './',
          paths: {
            '*': '*.js'
          }
        });
        
        System.import('app');
    </script>
  </body>
</html>