app.controller('ContactusController', ['$scope', 'ContentService', '$rootScope',
function($scope, ContentService, $rootScope){
    $scope.ads_images = $rootScope.ads_images;
    $scope.content = $rootScope.contents.contactus ? $rootScope.contents.contactus : '';
    $scope.loading = false;
}]);