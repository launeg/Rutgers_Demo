<!DOCTYPE html>
<html lang="en-US">
<!--HEAD******************************************************************************** -->

<head>
  <link type="text/css" href="../css/main.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
  <meta charset="utf-8">

  <title> SASN IT ACCESS REQUEST FORM LIST</title>
</head>


<!--This is to add the Angular  -->


<!--BODY********************************************************************************-->

<body>
  <header>
    <div class="navbar">
      <img src="../images/logo.png" alt="Rutgers Logo" class="image1"></img>
      <img src="../images/sprite_backgrounds.png" alt="Back Drop" class="image2"></img>
    </div>
  </header>


  <div class="form-container">

    <!--This is to add the Angular   
      <div ng-app="">
        <p>Name: <input type="text" ng-model="name"></p>
        <p ng-bind="name"></p>
      </div>
      -->
    <!--<h1>SASN IT ACCESS REQUEST FORM LIST</h1>-->


    <!--Search B-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <ul class="tabs">
      <li data-tab-target="#submission" class="active tab">Submitted</li>
      <div class="search_bar">
        <input type="text" placeholder="Search" name="search">
        <button type="submit"><i class="fa fa-search"></i></button>
      </div>
    </ul>

    
    <div class="tab-content">
      <div id="submission" data-tab-content class="active">
        <h1>Your Submissions</h1>
        <form action="action_page.php">

          <table class="table3"> <!--main idea, use the table info-->
            <tr>
              <th>Froms Inprogress</th>
              <th>Forms Approved</th>
              <th>Forms Denied</th>
              <!-- i need to use the info, I need to review this-->
            </tr>
            <tr>
              <td>1</td>
              <td>2</td>
              <td>1</td>
            </tr>
          </table>
          <br>

          <!-- <a href="#" class="previous round">&#8249;</a>
      <a href="#" class="next round">&#8250;</a> -->

          <br>
          <div class="drop-down-section">
            <div class="container">
              <label for="school-names">Schools</label>
              <select name=" school-names" id="school-names">
                <option value=SASN>School of Arts and Sciences-Newark</option>
                <option value="RBS">Rutgers Business School</option>
                <option value="Nursing">School of Nursing</option>
                <option value="SPAA">School of Public Affairs and Administration</option>
                <option value="SCJ">School of Criminal Justice</option>
              </select>
            </div>
            <div class="container">
              <label for="it-names">IT System:</label>
              <select name="it-names" id="it-names">
                <option value="">START</option>
                <option value="">GradTracker</option>
                <option value="">Junior Tracker</option>
                <option value="">AMS(PTL)</option>
                <option value="">Faculty Integration</option>
                <option value="">SASN Website</option>
                <option value="">Kronos</option>
              </select>
            </div>
            <div class="container">
              <label for="roles-names">Role</label>
              <select name="roles-names" id="roles-names">
                <option value="admin">Admin</option>
                <option value="supervisor">Supervisor</option>
                <option value="advisor">Student Adivsor</option>
                <option value="informational">Informational</option>
              </select>
            </div>
            <div class="container">
              <label for="act-names">Last Action</label>
              <select name="act-names" id="act-names">
                <option value=" "></option>
                <option value=" "></option>
                <option value=" "></option>
                <option value=" "></option>
              </select>
            </div>
          </div>

          <br>
          <div class="nav_buttons">
            <a href="#" class="previous">&laquo; Previous</a>
            <a href="#" class="next">Next &raquo;</a>
          </div>
          <div class="table-information">
            <table class = "table2">
              <thead>
                <tr>
                  <th>Date Sub</th>
                  <th>Date Eff</th>
                  <th>NetID</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>IT System</th>
                  <th>Roles</th>
                  <!-- is not just inprocess but, aproved by ..., ast thing thing that the form went throw-->
                  <th>School & Department</th>
                  <th>Supervisor's Name</th>
                  <th>Last Action</th><!-- Date Last Submitted ..-->
                  <th>Last Action's Date </th>
                </tr>
              </thead>

              <tbody>
                <?php 
                $host = "localhost";
                $dbname = "rutgersaccessgrantsystem";
                $username = "root";
                $password = "root";

                $conn = mysqli_connect($host,$username,$password,$dbname);

                if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
                }

                // was trying something else for the table JOINs
                // function toJoin($tableName, $rowName){
                //   $sql = "SELECT * FROM forms INNER JOIN " . $tableName . 
                //          " ON " . $tableName . "." . $rowName . " = forms" . "." . $rowName;
                  
                //   return $sql;
                // };
                
                // $tableNamesList = array(
                //   "roles" => "roles", 
                //   "schoolanddepartment" => "schoolanddepartment", 
                //   "application" => "application",
                //   "supervisorInfo" => "supervisorInfo",
                //   "formupdatelogtypes" => "formupdatelogtypes"
                // );

                // $rowNameValues = array(
                //   "roleID" => "roleID",
                //   "schoolDeptID" => "schoolDeptID",
                //   "systemID" => "systemID",
                //   "updateTypeID"=> "updateTypeID"
                // );

                $sq1 = "SELECT * FROM forms -- (Lucas added) INNER JOIN onto forms using the other files
                    INNER JOIN roles ON roles.roleID = forms.roleID 
                    INNER JOIN schoolanddepartment ON schoolanddepartment.schoolDeptID = forms.schoolDeptID
                    INNER JOIN application ON application.systemID = forms.systemID
                    JOIN supervisorInfo ON supervisorInfo.supervisorID = forms.supervisorID
                    INNER JOIN formupdatelogtypes ON formupdatelogtypes.updateTypeID = forms.requestID"; 
                $result = mysqli_query($conn, $sq1);

                if (!$result) {
                die("Invalid query: " . $connection-> error);
                }
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                function displayTableInfo($result){
                  while ($row = mysqli_fetch_assoc($result)){
                    echo "<tr>";
                      echo "<td>" . date('m-d-Y', strtotime($row['entryDate'])) . "</td>";
                      echo "<td>" . date('m-d-Y', strtotime($row['effDate'])) . "</td>";
                      echo "<td>" .$row['userNetID'] . "</td>";
                      echo "<td>" .$row['userFirstName'] . "</td>";
                      echo "<td>" .$row['userLastName'] . "</td>";
                      echo "<td>" .$row['systemName'] . "</td>"; // (Lucas changed)
                      echo "<td>" .$row['rolesNames'] . "</td>"; // (Lucas changed)
                      // echo "<td>" .$row['roleID'] . "</td>";
                      echo "<td>" .$row['schoolName'] . ' - '.$row['debtName']. "</td>"; // (Lucas changed)
                      echo "<td>" .$row['supervisorName'] . "</td>"; // (Lucas changed)
                      echo "<td>" .$row['actionName'] ."</td>"; // (Lucas changed)
                      echo "<td>" . date('m-d-Y', strtotime($row['effDate'])) . "</td>";
                    echo "</tr>";
                  
                  }    
                }
                displayTableInfo($result);
                ?> 
              </tbody>
              

            </table>
          </div>
          <script>
            document.addEventListener("DOMContentLoaded", function() {
              // grab all the rows in the table
              var rows = document.querySelectorAll("#phpTable tr");

              // Define column names as a JavaScript variable
              var columnNames = <?php echo json_encode(array_keys(mysqli_fetch_assoc($result))); ?>;

              rows.forEach(function(row, index) {
                row.addEventListener("click", function() {
                  var rowData = [];

                  //iterates through each cell in the row and pushes it into the array declared above
                  var cells = this.getElementsByTagName("td");
                  for (var j = 0; j < cells.length; j++) {
                    rowData.push(cells[j].textContent.trim());
                  }

                  // Create a URL with query parameters to pass row data to the new HTML file
                  var url = "form.htm?";
                  for (var k = 0; k < rowData.length; k++) {
                    url += columnNames[k] + "=" + encodeURIComponent(rowData[k]) + "&";
                  }

                  // Generate a unique name for the popup window
                  var popupName = "RowPopup" + index;

                  // Open the new HTML file with row data using the unique popup name
                  var popupWindow = window.open(url, popupName, "width=400,height=400");
                });
              });
            });
          </script>

          <script>
            function myFunction() {
              var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("myInput");
              filter = input.value.toUpperCase();
              table = document.getElementById("myTable");
              tr = table.getElementsByTagName("tr");

              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }
              }
            }
          </script>
          <!--Filter/Search List -->
          <script>
            function myFunction2() {
              // Declare variables
              var input, filter, ul, li, a, i, txtValue;
              input = document.getElementById('myInput');
              filter = input.value.toUpperCase();
              ul = document.getElementById("myUL");
              li = ul.getElementsByTagName('li');

              // Loop through all list items, and hide those who don't match the search query
              for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                  li[i].style.display = "";
                } else {
                  li[i].style.display = "none";
                }
              }
            }
          </script>

          <div class="nav_buttons">
            <a href="#" class="previous">&laquo; Previous</a>
            <a href="#" class="next">Next &raquo;</a>
          </div>
          <!-- Is not getting the info from the json file -->
          <div ng-app="myApp" ng-controller="myController">


            <!-- <table>
              <tr>
                <th>User ID</th>
                <th>NetID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Application</th>
              </tr>

              <tr ng-repeat="test in list">
                <td>{{test.userID}}</td>
                <td>{{test.netID}}</td>
                <td>{{test.firstName}}</td>
                <td>{{test.lastName}}</td>
                <td>{{test.phone}}</td>
                <td>{{test.email}}</td>
                <td>{{test.application}}</td>
                <-- Add the other items -->
              </tr>


            </table> 
          </div>
        </form>

      </div>
     
          <script>
            function myFunction() {
              var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("myInput");
              filter = input.value.toUpperCase();
              table = document.getElementById("myTable");
              tr = table.getElementsByTagName("tr");

              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }
              }
            }
          </script>
          <!--Filter/Search List -->
          <script>
            function myFunction2() {
              // Declare variables
              var input, filter, ul, li, a, i, txtValue;
              input = document.getElementById('myInput');
              filter = input.value.toUpperCase();
              ul = document.getElementById("myUL");
              li = ul.getElementsByTagName('li');

              // Loop through all list items, and hide those who don't match the search query
              for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                  li[i].style.display = "";
                } else {
                  li[i].style.display = "none";
                }
              }
            }
          </script>

          <!-- Is not getting the info from the json file -->
          <!-- <div ng-app="myApp" ng-controller="myController">


            <table>
              <tr>
                <th>User ID</th>
                <th>NetID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Application</th>
              </tr>

              <tr ng-repeat="test in list">
                <td>{{test.userID}}</td>
                <td>{{test.netID}}</td>
                <td>{{test.firstName}}</td>
                <td>{{test.lastName}}</td>
                <td>{{test.phone}}</td>
                <td>{{test.email}}</td>
                <td>{{test.application}}</td>
                <--  Add the other items -->
              </tr>
            </table>
          </div>
        </form>
      </div>

    </div>

  </div>
</body>

<!--The Controller-->
<!-- 
	<script>

    function myController($scope, $http) {
        var url = "http://localhost/AccessRequest/templates/test.json";
        $http.get(url).success(function (response) {
            $scope.list = response;
        });
    }
	</script>
	-->
<!-- <script>
  var app = angular.module('myApp', []);
  app.controller('myController', function ($scope, $http) {

    var request = {
      method: 'get',
      url: 'http://localhost/AccessRequest/templates/test.json',
    };

    $scope.arrExamples = new Array;

    $http(request)
      .success(function (data) {
        $scope.arrExamples = data;
        $scope.list = $scope.arrExamples;
      })
      .error(function () {

      });
  });

</script>
<script src="../js/src/tab.js"></script> -->

</html>