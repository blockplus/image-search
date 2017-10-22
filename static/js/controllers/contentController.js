app.controller('ContentController', ['$scope', 'AuthenticationService', 'ContentService',
function($scope, AuthenticationService, ContentService){
    $scope.initialize = function() {
        $scope.loading = false;

        $scope.about_content = "";
        $scope.policy_content = "";
        $scope.contactus_content = "";
    }

    $scope.logout = function(){
        AuthenticationService.Logout();
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