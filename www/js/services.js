'use strict';

/* Services */
angular.module('kollaborate.services', [])
  .factory('Message', function($http, User){
    return {
        connection: false,
        user: false,
        onMessageCallbacks: {},
        onMessage: function(action, callback){
            var self = this;
            if(action in self.onMessageCallbacks){
                self.onMessageCallbacks.push(callback);
            } else {
                self.onMessageCallbacks[action] = [ callback ];
            }
        },
        connect: function(ip, subscriptions){
            var self = this;
            if(!self.connection){
                self.connection = new WebSocket('ws://' + ip + ':8080');
                self.connection.onopen = function(e){
                    self.subscribe(subscriptions);
                    self.online();
                };
                self.connection.onmessage = function(e) {
                    var m = JSON.parse(e.data);
                    if(_.has(self.onMessageCallbacks, m.action)){
                       _.each(self.onMessageCallbacks[m.action], function(callback){
                            if(typeof callback == "function"){
                                callback(m);
                            }
                       });
                    }
                };
                self.user = User.current;
            }
        },
        online: function(){
            this.send('_system/user/online','');
        },
        subscribe: function(channels){
            var self = this;
            _.each(channels, function(channel){
                self.send('_system/subscribe/' + channel + '/ignore', '');
            });
        },
        send: function(route, message){
            var self = this;
            var payload = {
                route: route,
                message: message,
                from: self.user.username,
                signature: self.user.password
            };
            self.connection.send(JSON.stringify(payload));
        }
    }
  })
  .factory('Channel', function($http, User, Users, Message, $rootScope) {
    return {
        current: {},
        message: Message,
        users: Users,
        user: User,
        booted: false,
        all: [],
        init: function(){
           var self = this;
           if(!self.booted){
                self.booted = true;
                self.message.connect('localhost', _.pluck(self.all, 'channel'));
                self.message.onMessage('user', function(m){
                    console.log(m, self.users.all);
                    self.users.all[m.from].status = m.last_attribute;
                    $rootScope.$broadcast('socket:incoming');
                });
                self.message.onMessage('message', function(m){
                    if(_.has(self.all, m.channel)){
                        if(!_.has(self.all[m.channel], 'messages')){
                            self.all[m.channel].messages = [];
                        }
                        self.all[m.channel].messages.push(m);
                        $rootScope.$broadcast('socket:incoming', m);
                    }
                });
           }
        },
        join: function(channel, success_callback){
           var self = this;
           self.init();
           self.current = _.findWhere(self.all, {channel: channel});
           self.current = self.all[channel];
           self.users.inChannel(self.current.channel, success_callback);
        },
        fetchAll: function(success_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/user/_channels'})
            .success(function(data, status, headers, config) {
                var to_join = data[0].channel;
                self.all = _.indexBy(data, 'channel');
                self.join(to_join, success_callback);
            })
            .error(function(data, status, headers, config){
                self.all = {};
            });

        }
    }
  })
  .factory('User', function($http){
      return {
        current: {},
        isAuthenticated: function(){
            return typeof this.current.username == 'undefined' ? false : true;
        },
        authenticate: function(success_callback, error_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/user/_current'})
            .error(function(data, status, headers, config) {
                self.current = {};
                error_callback();
            })
            .success(function(data, status, headers, config){
                self.current = data;
                if(typeof success_callback == "function"){
                    success_callback();
                }
            });
        },
        logout: function(success_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/user/_logout'})
            .success(function(data){
                if(typeof success_callback == "function"){
                    success_callback();
                }
            });
        },
        login: function(username, password, success_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/user/' + username + '/login/' + password})
            .error(function(data, status, headers, config) {
                self.login_failed = true;
            })
            .success(function(data, status, headers, config){
                self.current = data;
                self.login_failed = false;
                if(typeof success_callback == "function"){
                    success_callback();
                }
            });
        }
      }
  }).
  factory('Users', function($http){
      return {
        current: [],
        all: [],
        avatar: function(username){
            var user = _.findWhere(this.all, {username:username});
            return user.avatar;
        },
        isInChannel: function(username){
            var self = this;
            return _.indexOf(self.current, username) >= 0;
        },
        inChannel: function(channel, success_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/channels/' + channel + '/users' })
            .success(function(data, status, headers, config){
                self.current = _.sortBy(data, function(d) { return d.status_code; });
                self.current = _.pluck(self.current, 'username');
                if(typeof success_callback == "function"){
                    success_callback();
                }
            });
        },
        fetchAll: function(success_callback){
            var self = this;
            $http({method: 'GET', url: 'index.php/users'})
            .success(function(data, status, headers, config){
                self.all = data;
                if(typeof success_callback == "function"){
                    success_callback();
                }
            });
        }
      }
  });
