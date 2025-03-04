(function() {
var pageApp = angular.module('pageApp',['ngRoute']);

pageApp.factory("AccessRequestSettings", AccessRequestSettings);
AccessRequestSettings.$inject = ["$http"];
function AccessRequestSettings($http) {
    var apiURL = '/AccessRequest/api/v1';
    return {
        baseURL: '/AccessRequest/',
        apiURL: apiURL
    };
}

//Add in future
/*
pageApp.config(['OAuthProvider','OAuthTokenProvider','$compileProvider','$httpProvider', 'Env', function(OAuthProvider, OAuthTokenProvider, $compileProvider, $httpProvider, Env) {
    OAuthTokenProvider.configure({
    	name: 'gttok',
    	options: {
    		secure: false
    	}
    }); //TODO generate
    OAuthProvider.configure({
    	baseUrl: Env.Host,
    	clientId: 'gtcandidate',
    	clientSecret: '086c27fa1827cc306c2935000037706714bd8eba64a83e8ce72760bbca6400e4',
    	grantPath: '/libfiles/oauth/token.php',
    	revokePath: '/libfiles/oauth/revoke.php'
    });
    

//$compileProvider.debugInfoEnabled(false);
//$compileProvider.commentDirectivesEnabled(false);
//$compileProvider.cssClassDirectivesEnabled(false);
}]);
*/

pageApp.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when("/", {
        templateUrl: "templates/main.htm",
        controller: 'IndexCtrl'
    })
    .when("/form/:formID?", {
        templateUrl: "templates/form.htm",
        controller: 'FormCtrl'
    })
    .otherwise({
        redirectTo: '/'
    });
}]);

pageApp.factory("Example2Svc", Example2Svc);
Example2Svc.$inject = ["$q", "$http", "AccessRequestSettings"];
function Example2Svc($q, $http, AccessRequestSettings) {
    var svc = {};

    svc.model = svc.model || {
        name: "",
        shortName: "",
        class: ""
    };

    svc.someAPIFunction = function(user) {
        var data = {
            "username": user
        }
        return $http.get(AccessRequestSettings.apiURL+'/example2/', data)
        .then(function(response) {
            return response.data;
        });
    };
    
    svc.submit = function(ex) {
    	var data = {
    		"exampleSubmit": ex
    	};
    	return $http.post(AccessRequestSettings.apiURL+'/example2/submitForm/', data)
        .then(function(response) {
            return response.data;
        });
    }

    return svc;
};

pageApp.controller("ParentCtrl", ParentCtrl);
ParentCtrl.$inject = ["AccessRequestSettings", "$scope"];
function ParentCtrl(AccessRequestSettings, $scope) {
    $scope.var = null;

    $scope.someFunction = function() {

    };
};

pageApp.controller("IndexCtrl", IndexCtrl);
IndexCtrl.$inject = ["AccessRequestSettings", "$scope", "Example2Svc"];
function IndexCtrl(AccessRequestSettings, $scope, Example2Svc) {
    $scope.var = null;
    $scope.data = null;

    $scope.someFunction = function() {
        var state = Example2Svc.someAPIFunction().then( function( response ) {
            $scope.data = response;
        }, function( error ) {
            alert("did not work");
        });
    };

    function init() {
        $scope.someFunction();
    };

    init()
};

pageApp.controller("FormCtrl", FormCtrl);
FormCtrl.$inject = ["AccessRequestSettings", "$scope", "$routeParams", "AppSvc"];
function FormCtrl(AccessRequestSettings, $scope, $routeParams, AppSvc) {
    $scope.var = null;
    $scope.data = null;
    $scope.ex = null;

    $scope.submit = function($event) {
        var state = AppSvc.submit($scope.ex).then( function( response ) {
            $scope.data = response;
            alert("Saved "+response);
        }, function( error ) {
            alert("did not work");
        });
    };
    
    $scope.loadForm = function(formID) {
    	
    };

    function init() {
        $scope.loadForm($routeParams['formID']);
    };

    init()
};
})();
