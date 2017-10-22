app.controller('SearchController', ['$scope', 'searchService', '$state', '$uibModal', '$rootScope',
function($scope, searchService, $state, $uibModal, $rootScope){
    $scope.Init = function() {
        $scope.ads_images = $rootScope.ads_images;
        $scope.bank_image_count = $rootScope.bank_count;

        $scope.loading = false;
        $scope.url = "";

        $scope.matches = $state.params.matches;
        $scope.origin = $state.params.origin;
        $scope.count = $state.params.count;

        if ($scope.origin.filename)
            $scope.url = $scope.origin.filename;

        $scope.currentPage = 1;
        $scope.maxSize = 5;
    }
    $scope.Init();

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
      };

      $scope.pageChanged = function() {
        searchService.searchPage($scope, $scope.origin.image, $scope.currentPage, $scope.afterPageSearch);
      };

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
        $scope.count = result.total_count;
        $scope.matches = result.matches;
        $scope.origin = result.origin;

        $scope.url = $scope.origin.filename;

        $scope.currentPage = 1;
    }
    $scope.afterPageSearch = function(result) {
        $scope.count = result.total_count;
        $scope.matches = result.matches;
    }

    $scope.compare = function(item) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'templates/compareItemsModal.html',
            controller: 'CompareItemsController',
            size: 'lg',
            resolve: {
                item: function() {
                    return {
                        'compare_image' : item.image, 
                        'origin_image' : $scope.origin.image
                    };
                }
            }
        });
    }


}]);
