'use strict';

// Declare app level module which depends on filters, and services
angular.module('kollaborate', [
  'kollaborate.services',
  'kollaborate.controllers',
  'ngRoute'
])
.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/', {templateUrl: 'partials/loading.html', controller: 'LoadingCtrl'});
  $routeProvider.when('/login', {templateUrl: 'partials/login.html', controller: 'LoginCtrl'});
  $routeProvider.when('/channel/:channel', {templateUrl: 'partials/channel.html', controller: 'ChannelCtrl'});
  $routeProvider.otherwise({redirectTo: '/'});
}]);
