app.controller('UploadController', ['$scope', 'addImage', 'batchImages', 'AuthenticationService',
function($scope, addImage, batchImages, AuthenticationService){
    $scope.initialize = function() {
        $scope.myFile = "";
        $scope.title = "";
        $scope.description = "";
        $scope.link = "";
        $scope.excel_name = "";

        $scope.loading = false;
    }

    $scope.logout = function(){
        AuthenticationService.Logout();
    };

    $scope.afterAdd = function() {
        $scope.title = "";
        $scope.description = "";
        $scope.link = "";
        $scope.excel_name = "";

        $scope.loading = false;
    }

    $scope.upload = function(){
        var file = $scope.myFile;
        var title = $scope.title;
        var description = $scope.description;
        var link = $scope.link;

        if(!file || !title || !description || !link)
            return;

        var addUrl = "/add_image";
        addImage.uploadFileToUrl($scope, addUrl, file, title, description, link, $scope.afterAdd);
    };

    $scope.batch_process = function(){
        var excel_name = $scope.excel_name;

        if(!excel_name)
            return;

        var url = "/batch_images";
        batchImages.doAPI($scope, url, excel_name, $scope.afterAdd);
    };

    $scope.initialize();
}]);