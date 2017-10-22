app.controller('AboutController', ['$scope', 'ContentService', '$rootScope',
function($scope, ContentService, $rootScope){
    $scope.ads_images = $rootScope.ads_images;
    $scope.content = $rootScope.contents.about ? $rootScope.contents.about : '';
    $scope.loading = false;
}]);