<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PHP Explorer Tree</title>
        <link rel="stylesheet" href="resource/css/bootstrap.min.css">
        <link rel="stylesheet" href="resource/css/bootstrap-responsive.min.css">
                
        <link data-require="ng-table@*" rel="stylesheet" href="resource/css/ng-table.css" />
        <link rel="stylesheet" href="resource/css/style.css">

        

    </head>
    <body ng-app="myModule">
        <?php
            $dirs = scandir('./');
            $allData = array();
            if (!empty($dirs)) {
                $i = 0;
                foreach ($dirs as $index => $folder) {
                    if ($folder == '.')
                        continue;
                    $fStat = stat($folder);
                    if(is_file($folder)){
                        $ext = explode('.', $folder);
                        $ext = array_reverse($ext);
                        $ext = $ext[0];
                    }else{
                        $ext = 'dir';
                    }
                    
                    $allData[] = array('index' => ++$i,'ftype'=>$ext, 'url' => trim($folder), 'ctime' => date("Y-m-d H:i:s", $fStat['ctime']), 'mtime' => date("Y-m-d H:i:s", $fStat['mtime']));
                }
            }
            $jsonData = json_encode($allData);
        ?>

            <div class="navbar">
              <div class="navbar-inner">
                <a class="brand" href="#">Project Explorer</a>
                <ul class="nav">
                    <li class="active"><a href='./' >Project List</a></li>
                  <li ><a href='/phpmyadmin' target="_blank">Php MyAdmin</a></li>
                  
                </ul>
              </div>
            </div>

            <div class="container-fluid" ng-controller="Dirs">
              <div class="row-fluid">
                <div class="span2"></div>
                <div class="span8">
                  <table ng-table="tableParams" show-filter="true" class="table">
                    <tr ng-repeat="folder in $data">
                        <td data-title="'SI.'" sortable="'index'" style="text-align:center;width: 10%; ">{{$index+((tableParams.page()-1)*tableParams.count())+1}}</td>
                        <td sortable="'url'" data-title="'Project Name'" class='ftype ' style="background: url(resource/img/{{folder.ftype}}.png) left center no-repeat; width: 65%; "  filter="{ 'url': 'text' }"><a href='{{folder.url}}' >{{folder.url}}</a></td> 
                        <td data-title="'Type'" filter="{ 'ftype': 'text' }" style="text-align:center;width: 10%; ">{{folder.ftype}}</td>
                        <td data-title="'Modified Time'"  sortable="'mtime'"  style="text-align:center;width: 15%;">{{folder.mtime}}</td>
                    </tr>
                </table>
                </div>
                <div class="span2"></div>
              </div>
            </div>

        

        <footer class="footer">
            <div class="container">
                <p class="text-muted"><?php echo date('Y') ?> &COPY; Eather</p>
            </div>
        </footer>
        <script src="resource/js/jquery.min.js"></script>
        <script src="resource/js/angular.min.js"></script>
        <script src="resource/js/ng-table.js"></script>


        <script>
            var app = angular.module('myModule', ['ngTable']).controller('Dirs', function ($scope, $filter, ngTableParams) {
                var data = <?php echo $jsonData; ?>;
                $scope.tableParams = new ngTableParams({
                    page: 1,            // show first page
                    count: 15,         // count per page
                    sorting: {
                            index: 'asc'     // initial sorting
                        },
                    filter: {
                            ftype:'dir'
                        }
                }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                        // use build-in angular filter
                        var filteredData = params.filter() ?
                                $filter('filter')(data, params.filter()) :
                                data;
                        var orderedData = params.sorting() ?
                            $filter('orderBy')(filteredData, params.orderBy()) :
                            data;
                        params.total(orderedData.length); // set total for recalc pagination
                        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                    }
                });
            });

        </script>
    </body>
</html>