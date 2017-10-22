var app = angular.module("myApp", ['ngSanitize', 'ui.router','ngAnimate','ui.bootstrap', 'ngCookies', 'ui.router.state.events']); 

app.run(['$rootScope', 'Auth', '$state', 'GlobalService',
function ($rootScope, Auth, $state, GlobalService) {
    $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
        if (!Auth.isLoggedIn()) {
                if (toState.name === 'upload'
                    || toState.name === 'browse'
                    || toState.name === 'content'
                    || toState.name === 'advertise') {
                    event.preventDefault();
                    $state.go('login');
                }
        }
        /*
        if (toState.name === 'register') {
            event.preventDefault();
            $state.go('login');
        }//*/

    }); 
    $rootScope.bank_count = 0;
    $rootScope.ads_images = new Array();
    $rootScope.contents = new Array();

    GlobalService.loadGlobalData($rootScope);
}]);

app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/welcome');

    $stateProvider
    .state('welcome', {
        url: '/welcome',
        templateUrl : "templates/welcome.htm",
        controller  : 'WelcomeController'
    })
    .state('search', {
        url: '/search',
        templateUrl : "templates/search.htm",
        controller  : 'SearchController',
        params: { 
            matches: [], 
            origin: new Array(),
            count: 0
        }
    })
    .state('home', {
        url: '/home',
        templateUrl : "templates/home.htm",
        controller  : 'HomeController'
    })
    .state('upload', {
        url: '/upload',
        templateUrl : "templates/upload.htm",
        controller  : 'UploadController'
    })
    .state('content', {
        url: '/content',
        templateUrl : "templates/content.htm",
        controller  : 'ContentController'
    })
    .state('advertise', {
        url: '/advertise',
        templateUrl : "templates/advertise.htm",
        controller  : 'AdvertiseController'
    })
    .state('browse', {
        url: '/browse',
        templateUrl : "templates/browse.htm",
        controller  : 'BrowseController'
    })
    .state('login', {
        url: '/login',
        templateUrl : "templates/login.htm",
        controller  : 'LoginController'
    })
    .state('register', {
        url: '/register',
        templateUrl : "templates/register.htm",
        controller  : 'RegisterController'
    })
    .state('about', {
        url: '/about',
        templateUrl : "templates/about.htm",
        controller  : 'AboutController'
    })
    .state('policy', {
        url: '/policy',
        templateUrl : "templates/policy.htm",
        controller  : 'PolicyController'
    })
    .state('contactus', {
        url: '/contactus',
        templateUrl : "templates/contactus.htm",
        controller  : 'ContactusController'
    });
});

app.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);

app.directive('customOnChange', function() {
  return {
    restrict: 'A',
    link: function (scope, element, attrs) {
      var onChangeHandler = scope.$eval(attrs.customOnChange);
      element.bind('change', onChangeHandler);
    }
  };
});

app.directive('loading', function () {
      return {
        restrict: 'E',
        replace:true,
        template: '<div class="loading"><img src="http://www.nasa.gov/multimedia/videogallery/ajax-loader.gif" class="loading-img"/></div>',
        link: function (scope, element, attr) {
              scope.$watch('loading', function (val) {
                  if (val)
                      $(element).show();
                  else
                      $(element).hide();
              });
        }
      }
  });

app.directive('myEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.myEnter);
                });

                event.preventDefault();
            }
        });
    };
});

app.service('searchService', ['$http', '$window', function ($http, $window) {
    this.searchFile = function($scope, file, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('file', file);
        $http.post("/search_image", fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            if (result == null) {
                $window.alert("No matching image found!");
                return;
            }
            callback(result);
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to search images!");
        });
    }

    this.searchPage = function($scope, image, page, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('image', image);
        fd.append('page', page);
        $http.post("/search_image_page", fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            if (result == null) {
                $window.alert("No matching image found!");
                return;
            }
            callback(result);
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to search images!");
        });
    }

    this.searchUrl = function($scope, url, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('url', url);
        $http.post("/search_url", fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            if (result == null) {
                $window.alert("No matching image found!");
                return;
            }
            callback(result);
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to search images!");
        });
    }

}]);

app.service('addImage', ['$http', '$window', function ($http, $window) {
    this.uploadFileToUrl = function($scope, uploadUrl, file, title, description, link, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('title', title);
        fd.append('description', description);
        fd.append('link', link);
		fd.append('file', file);
        
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(data){
            $scope.loading = false;
            if (data == "ok") {
        	   $window.alert("Uploaded successfully!");
                callback();
            } else if (data == "existing") {
                $window.alert("Already existing!");
            }
        })
        .error(function(){
            $scope.loading = false;
        	$window.alert("Failed to upload to the server!");
        });
    }
}]);

app.service('batchImages', ['$http', '$window', function ($http, $window) {
    this.doAPI = function($scope, apiUrl, excel_name, callback){
        $scope.loading = true;
        var fd = new FormData();
        fd.append('excel_name', excel_name);
        
        $http.post(apiUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(){
            $scope.loading = false;
            $window.alert("Batch processed!");
            callback();
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to process batch images!");
        });
    }
}]);

app.service('browseService', ['$http', '$window', function ($http, $window) {
    this.browse = function($scope, page, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('page', page);
        
        $http.post('/browse_images', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            callback(result);
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to get images!");
        });
    }
}]);

app.service('editItemService', ['$http', '$window', function ($http, $window) {
    this.editItem = function($scope, item, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('image', item.image);
        fd.append('title', item.title);
        fd.append('description', item.description);
        fd.append('link', item.link);
        
        $http.post('/edit_item', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            callback();
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to update item!");
        });
    }

    this.deleteItem = function($scope, item, callback) {
        $scope.loading = true;
        var fd = new FormData();
        fd.append('image', item.image);
        
        $http.post('/delete_item', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            callback();
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to delete item!");
        });
    }
}]);

app.service('AuthenticationService', ['$http', '$window', 'Auth', function ($http, $window, Auth) {
    this.Login = function($scope, username, password) {
        $scope.loading = true;
        
        var fd = new FormData();
        fd.append('username', username);
        fd.append('password', password);
        
        $http.post('/login', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;

            if (result['error'] == "ok") {
                Auth.setUser(result['user']);
                $window.location.href = '/#/browse';
            }
            else if (result['error'] == "invalid username") {
                $window.alert("Invalid username");
            }
            else if (result['error'] == "invalid password") {
                $window.alert("Invalid password");
            }
            else{
                $window.alert("Login error!");
            }

        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Login Failed!");
        });
    }

    this.Logout = function() {
        $http.get('/logout', null, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            Auth.removeUser();
            $window.location.href = '/#/';
        })
        .error(function(){
            Auth.removeUser();
            $window.location.href = '/#/';
        });
    }

    this.Register = function($scope, firstname, lastname, username, password) {
        $scope.loading = true;
        
        var fd = new FormData();
        fd.append('firstname', firstname);
        fd.append('lastname', lastname);
        fd.append('username', username);
        fd.append('password', password);
        
        $http.post('/register', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;

            console.log(result);
            if (result == "ok") {
                $window.location.href = '/#/login';
            } else if (result == "existing") {
                $window.alert("Username already existing!");
            } else {
                $window.alert("Failed registration!");
            }
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Register Failed!");
        });
    }
}]);

app.factory('Auth', function($cookieStore){

    return{
        setUser : function(aUser){
            // Put cookie
          $cookieStore.put('user',aUser);
        },
        removeUser : function(){
          // Put cookie
          $cookieStore.remove('user');
        },
        isLoggedIn : function(){
          // Get cookie
          var user = $cookieStore.get('user');
          return(user)? user : false;
        }
      }
})

app.service('ContentService', ['$http', '$window',
function ($http, $window) {
    this.getContentsAll = function($scope, callback){
        $scope.loading = true;

        $http.post('/get_contents_all', "", {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            $scope.set_contents_all(result);
        })
        .error(function(){
            $scope.loading = false;
        });
    }

    this.getContent = function($scope, key){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('key', key);
        
        $http.post('/get_content', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            $scope.afterSearch(result);
        })
        .error(function(){
            $scope.loading = false;
        });
    }

    this.SaveContent = function($scope, key, content, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('key', key);
        fd.append('content', content);
        
        $http.post('/save_content', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            $window.alert("Successfully saved content!");
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to save content!");
        });
    }
}]);

app.service('UtilsService', [
function () {
    this.GetBigImage = function(thumb_image){
        var items = thumb_image.split("/");
        var index = items.indexOf('thumb');
        if (index > -1) {
            items.splice(index, 1);
        }
        var big_image = items.join("/");
        return big_image;
    }
}]);

app.service('AdminService', ['$http', '$window', '$state',
function ($http, $window) {
    this.addAderviseFile = function($scope, file, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('file', file);
        
        $http.post('/add_advertise_file', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(data){
            $scope.loading = false;
            if (data == "ok") {
               $window.alert("Added successfully!");
               callback();
            } else if (data == "existing") {
                $window.alert("Already existing!");
            }
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to upload to the server!");
        });
    }

    this.addAderviseUrl = function($scope, url, callback){
        $scope.loading = true;

        var fd = new FormData();
        fd.append('url', url);
        
        $http.post('/add_advertise_url', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(data){
            $scope.loading = false;
            if (data == "ok") {
               $window.alert("Added successfully!");
               callback();
            } else if (data == "existing") {
                $window.alert("Already existing!");
            }
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to upload to the server!");
        });
    }

    this.browse = function($scope, callback){
        $scope.loading = true;

        $http.post('/browse_images_advertise', '', {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            callback(result);
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to get images!");
        });
    }
    this.deleteItem = function($scope, item, callback) {
        $scope.loading = true;
        var fd = new FormData();
        fd.append('image', item.image);
        
        $http.post('/delete_image_advertise', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(result){
            $scope.loading = false;
            callback();
        })
        .error(function(){
            $scope.loading = false;
            $window.alert("Failed to delete item!");
        });
    }
    
}]);

app.service('GlobalService', ['$http',
function ($http, $window) {
    this.loadGlobalData = function($rootScope){
        $http.post('/load_global_data', '', {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(data){
            $rootScope.bank_count = data['bank_count'];
            $rootScope.ads_images = data['ads_images'];
            $rootScope.contents = data['contents'];
        })
        .error(function(){
            $rootScope.bank_count = 0;
            $rootScope.ads_images = new Array();
            $rootScope.contents = new Array();
        });
    }
}]);