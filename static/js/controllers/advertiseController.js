app.controller('AdvertiseController', ['$scope', 'AuthenticationService', 'AdminService',
function($scope, AuthenticationService, AdminService){
    $scope.initialize = function() {
        $scope.image_link = "";
        $scope.loading = false;

        $scope.images = [];
    }

    $scope.logout = function(){
        AuthenticationService.Logout();
    };

    $scope.initialize();

    $scope.uploadFile = function(){
        var file = $scope.myFile;
        if (!file) {
            alert("Please choose file!");
            return;
        }

        AdminService.addAderviseFile($scope, file, $scope.browse);
    };
    $scope.addUrl = function(){
        if (!$scope.image_link) {
            alert("Please input image link!");
            return;
        }
        AdminService.addAderviseUrl($scope, $scope.image_link, $scope.browse);
    };

    $scope.browse = function(){
        AdminService.browse($scope, $scope.afterSearch);
    };

    $scope.afterSearch = function(result) {
        $scope.images = result.images;
    }

    $scope.delete = function(item){
        AdminService.deleteItem($scope, item, function(){
            var index = $scope.images.indexOf(item);
            $scope.images.splice(index, 1); 
            $scope.count --;
        });
    };


    $scope.browse();

}]);