'use strict';

/* Controllers */

angular.module('kollaborate.controllers', [])
  .controller('ProfileCtrl', function($scope, User, Channel, $location) {
    $scope.user = User;
    $scope.channels = Channel;
    $scope.logout = function(){
        $scope.user.logout(function(){
            $location.path('/');
        });
    }
  })
  .controller('ChannelCtrl', function($scope, User, Channel, $location, $routeParams) {
    $scope.user = User;
    $scope.channels = Channel;
    $scope.send = function($event){
        if($event.keyCode == 13 && $scope.channels.message.connection && $scope.message_to_send !=""){
            $scope.channels.message.send($scope.channels.current.channel + '/message', $scope.message_to_send);
            $scope.message_to_send = '';
        }
    }

    $scope.$on('socket:incoming', function($event, m){
        $scope.$apply();
    });

    if(!$scope.user.isAuthenticated()){
        $location.path('/');
    } else {
        $scope.channels.join($routeParams.channel);
    }
  })
  .controller('LoadingCtrl', function($scope, User, Channel, $timeout, $location) {
    $scope.user = User;
    $scope.channels = Channel;
    $scope.channels.users.fetchAll(function(){
        $scope.user.authenticate(function(){
            /*Success!*/
            $scope.channels.fetchAll(function(){
                $location.path('/channel/' + $scope.channels.current.channel);
            });
        }, function(){
            /*Boo*/
            $timeout(function(){$location.path('/login');}, 1000);
        });
    });
  })
  .controller('LoginCtrl', function($scope, User, Users, $location){
    $scope.users = Users;
    $scope.user = User;
    $scope.search = '';
    $scope.user_found = false;

    $scope.foundUser = function(username){
      $scope.user_found = username;
      $scope.search = username;
    };

    $scope.login = function($event){
        if($event.keyCode == 13){
            $scope.user.login($scope.user_found, $scope.password, function(){
                $location.path('/home');
            });
        }
    };
});
