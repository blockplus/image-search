app.controller('CompareItemsController', ['$scope','item','$uibModalInstance', 'UtilsService',
function ($scope, item, $uibModalInstance, UtilsService) {

      $scope.origin_image = UtilsService.GetBigImage(item.origin_image);
      $scope.compare_image = UtilsService.GetBigImage(item.compare_image);

      $scope.ok = function () {
        $uibModalInstance.dismiss('cancel');
      };
}]);