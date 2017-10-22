app.controller('WelcomeController', ['$scope', 'searchService', '$state',
function($scope, searchService, $state){
    $scope.Init = function() {
        $scope.loading = false;
        $scope.url = "";
    }
    $scope.Init();

    $scope.searchFile = function(event){
        var files = event.target.files;
        if (!files || files.length == 0) {
            alert("Please choose file!");
            return;
        }
        var file = files[0]
        searchService.searchFile($scope, file, $scope.afterSearch);
    };

    $scope.searchUrl = function(){
        searchService.searchUrl($scope, $scope.url, $scope.afterSearch);
    };
    $scope.afterSearch = function(result) {
        $state.go('search',{matches: result.matches, origin: result.origin, count: result.total_count});
    }
}]);
