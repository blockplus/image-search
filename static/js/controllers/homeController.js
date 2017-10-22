app.controller('HomeController', ['$scope', 'searchService', 'AuthenticationService', 'Auth','$state', 
function($scope, searchService, AuthenticationService, Auth, $state){

    $scope.matches = [];
    $scope.loading = false;

    $scope.logout = function(){
        AuthenticationService.Logout();
    };

    $scope.match = function(){
        var file = $scope.myFile;
        if (!file) {
        	alert("Please choose file!");
        	return;
        }

        var searchUrl = "/search_image";
        $scope.loading = true;
        searchService.uploadFileToUrl($scope, file, searchUrl, $scope.afterSearch);
    };

    $scope.afterSearch = function(result) {
        $scope.matches = result;
    }
}]);
