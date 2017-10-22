app.controller('RegisterController', ['$scope', 'AuthenticationService', function($scope, AuthenticationService){
    $scope.init = function() {
        $scope.loading = false;
        $scope.firstname = "";
        $scope.lastname = "";
        $scope.username = "";
        $scope.password = "";
    }

    $scope.init();
    
    $scope.register = function() {
        AuthenticationService.Register($scope, $scope.firstname, $scope.lastname, $scope.username, $scope.password);
    }
}]);