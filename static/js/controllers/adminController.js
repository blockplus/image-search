app.controller('AdminController', ['$scope', 'addImage', 'batchImages', 'AuthenticationService', 'ContentService',
function($scope, addImage, batchImages, AuthenticationService, ContentService){
    $scope.initialize = function() {
    	$scope.myFile = "";
    	$scope.title = "";
    	$scope.description = "";
    	$scope.link = "";
        $scope.excel_name = "";

        $scope.loading = false;

        $scope.about_content = "";
        $scope.policy_content = "";
        $scope.contactus_content = "";
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

    $scope.match = function(){
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
    
    $scope.save_content = function(key, content) {
        ContentService.SaveContent($scope, key, content, $scope.afterSave);
    }
    $scope.afterSaveAbout = function(result) {
        console.log(result);
    }

    $scope.get_contents_all = function() {
        ContentService.getContentsAll($scope);
    }
    $scope.set_contents_all = function(result) {
        $scope.about_content = result['about'] ? result['about'] : '';
        $scope.policy_content = result['policy'] ? result['policy'] : '';
        $scope.contactus_content = result['contactus'] ? result['contactus'] : '';
    }

    $scope.initialize();
    $scope.get_contents_all();

}]);