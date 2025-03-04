<!DOCTYPE html>
<html ng-app="pageApp" ng-strict-di="" class="ng-scope">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SASN-IT Access Request Build 1</title>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="//cdn.usebootstrap.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/base.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js"></script>
		<script type="text/javascript" src="//cdn.usebootstrap.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.3/angular.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/angular-route/1.8.3/angular-route.js" integrity="sha512-tfrAMEcgRMbx1MeQrt2H/TrwaKG+m0ngUQ7R+hM9ZLD3SbwAvXsL1p0DwbTwv05EfES22HtfIged4iuHqmWYsw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script type="text/javascript" src="./js/src/accessrequest.js"></script>
    <style type="text/css"></style>
  </head>

  <body style="padding-bottom: 32px;">
    <div id="ru-navbar" class="navbar navbar-default">
      <div id="ru-navbar-inner">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-parent="#all-menu" data-target=".navbar_collapsed_data" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div class="navbar-header">
              <a class="navbar-left" href="https://www.rutgers.edu/">
                <img id="logo" class="img-responsive" src="./images/logo.png">
              </a>
            </div>
          </div>

          <div class="nav-collapse navbar_collapsed_data">
            <ul class="nav navbar-nav">
              <li><a href="https://newark.rutgers.edu/">UniversityWide</a></li>
              <li><a href="https://newbrunswick.rutgers.edu/">New Brunswick</a></li>
              <li><a href="https://camden.rutgers.edu/">Camden</a></li>
              <li><a href="https://academichealth.rutgers.edu/">RBHS</a></li>

            </ul>
          </div>
          <!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <nav id="navbar" class="navbar navbar-inverse header-fixed ng-scope">

      <div id="navbar-inner">
        <div class="navbar-header"><a class="navbar-brand" href="">Welcome</a></div>
      </div>
      <overlay id="splashOverlay" templatehtml="splashTemplate" show-on="showSplash" close="false" style="display: none;">
        <div class="overlay animate-show-element animate-show-hide"><button class="overlay-close btn no-print ng-hide" ng-click="showSplash = false" ng-show="false">×</button><!-- ngInclude: -->
        </div>
      </overlay>
    </nav>
    <!-- End of NAVBAR -->

    <div id="container" class="container-fluid">
      <div id="content" class="content-center">


		<!-- ACTUAL WORKING PART -->
		<h1>
      SASN-IT Access Request
    </h1>
		
		<div ng-view></div>
		
			<p>

    	</p>
		
        

      </div>
    </div>
    <div id="footer" class="well no-print">
      <p>
        Copyright ©2015, Rutgers, The State University of New Jersey, an equal opportunity, affirmative action institution. All rights reserved.
      </p>
    </div>
  </body>
</html>