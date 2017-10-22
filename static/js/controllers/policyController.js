app.controller('PolicyController', ['$scope', 'ContentService', '$rootScope',
function($scope, ContentService, $rootScope){
    $scope.ads_images = $rootScope.ads_images;
    $scope.content = $rootScope.contents.policy ? $rootScope.contents.policy : '';
    $scope.loading = false;
}]);