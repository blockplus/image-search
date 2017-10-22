app.controller('EditItemController', ['$scope','item','$uibModalInstance',  function ($scope, item, $uibModalInstance) {

    console.log($uibModalInstance);

      $scope.item = {
            'image': item.image,
            'title': item.title,
            'description': item.description,
            'link': item.link
        };

      $scope.ok = function () {
        $uibModalInstance.close($scope.item);
      };

      $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
      };
}]);