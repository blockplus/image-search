app.controller('LoginController', ['$scope', 'AuthenticationService', function($scope, AuthenticationService){
    $scope.init = function() {
        $scope.loading = false;
        $scope.username = "";
        $scope.password = "";
    }

    $scope.init();

    $scope.login = function () {
        AuthenticationService.Login($scope, $scope.username, $scope.password);
    }
}]);
