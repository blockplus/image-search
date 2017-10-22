app.controller('BrowseController', ['$scope', 'browseService','$uibModal', 'editItemService', 'AuthenticationService', '$rootScope',
function($scope, browseService,$uibModal, editItemService, AuthenticationService, $rootScope){
    $scope.init = function() {
        $scope.images = [];
        $scope.count = $rootScope.bank_count;
        $scope.loading = false;

        $scope.currentPage = 1;
        $scope.maxSize = 5;
    }

      $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
      };

      $scope.pageChanged = function() {
        console.log('Page changed to: ' + $scope.currentPage);
        $scope.browse();
      };


    $scope.logout = function(){
        AuthenticationService.Logout();
    };

    $scope.browse = function(){
        browseService.browse($scope, $scope.currentPage, $scope.afterSearch);
    };

    $scope.afterSearch = function(result) {
        $scope.count = result.count;
        $rootScope.bank_count = result.count;
    	$scope.images = result.images;
    }

    $scope.edit = function(item){

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'templates/editItemModal.html',
            controller: 'EditItemController',
            size: 'lg',
            resolve: {
                item: function() {
                    return item;
                }
            }
        });

        modalInstance.result.then(function (res_item) {
            editItemService.editItem($scope, res_item, function () {
                item.title = res_item.title;
                item.description = res_item.description;
                item.link = res_item.link;
            });
        }, function () {
            //$log.info('Modal dismissed at: ' + new Date());
        });
    };

    $scope.delete = function(item){
        editItemService.deleteItem($scope, item, function(){
            var index = $scope.images.indexOf(item);
            $scope.images.splice(index, 1); 
            $scope.count --;
        });
    };

    $scope.init();
    $scope.browse();
}]);