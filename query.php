<?php  
include 'connect.php';

if(isset($_POST['getArea'])){
      $id_simulation = $_POST['id_simulation'];
      $result_array = Array();
      $query = (mysqli_query($conn,"SELECT * FROM _area_used au INNER JOIN _area a ON au.id_area = a.id WHERE a.is_active = 1 AND au.id_simulation = $id_simulation"));

      while ($row = mysqli_fetch_assoc($query)) {
            array_push($result_array, $row);
      }
      echo $json_array = json_encode($result_array);
}
else if(isset($_POST['getDriver'])){
      $id_simulation = $_POST['id_simulation'];
      $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_batch as id FROM _batch WHERE id_simulation = $id_simulation AND is_current_batch = 1 LIMIT 1"));
      $id_batch = $sql['id'];

      $result_array = Array();
      $query = (mysqli_query($conn,"SELECT * FROM _generate_drivers gd JOIN _driver d ON gd.id_driver = d.id_driver WHERE gd.id_simulation = $id_simulation AND d.status = 1 AND gd.id_batch <= $id_batch AND gd.id NOT IN ( SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id_simulation AND (status = 2 OR status = 3) )"));
      while ($row = mysqli_fetch_assoc($query)) {
            array_push($result_array, $row);
      }
      echo $json_array = json_encode($result_array);
}
else if(isset($_POST['getPassenger'])){
      $id_simulation = $_POST['id_simulation'];
      $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_batch as id FROM _batch WHERE id_simulation = $id_simulation AND is_current_batch = 1 LIMIT 1"));
      $id_batch = $sql['id'];

      $result_array = Array();
      $query = (mysqli_query($conn,"SELECT * FROM _generate_passengers gp JOIN _passenger p ON gp.id_passenger = p.id_passenger  WHERE gp.id_simulation = $id_simulation AND gp.id_batch <= $id_batch AND p.status = 1 AND gp.id NOT IN ( SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id_simulation AND (status = 2 OR status = 3) )"));
      while ($row = mysqli_fetch_assoc($query)) {
            array_push($result_array, $row);
      }
      echo $json_array = json_encode($result_array);
}
else if(isset($_POST['getPickup'])){
      $id_simulation = $_POST['id_simulation'];
      $result_array = Array();
      $query = (mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_drivers gn ON a.id_generate_driver = gn.id JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id  WHERE a.id_simulation = $id_simulation AND a.status = 1"));
      while ($row = mysqli_fetch_assoc($query)) {
            array_push($result_array, $row);
      }
      echo $json_array = json_encode($result_array);
}
else if(isset($_POST['getOTW'])){
      $id_simulation = $_POST['id_simulation'];
      $result_array = Array();
      $query = (mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_drivers gn ON a.id_generate_driver = gn.id JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id  WHERE a.id_simulation = $id_simulation AND a.status = 2"));
      while ($row = mysqli_fetch_assoc($query)) {
            array_push($result_array, $row);
      }
      echo $json_array = json_encode($result_array);
}

else if(isset($_POST['setFinishPickup'])){
      $id_simulation = $_POST['id_simulation'];
      $result_array = Array();
      $now = date('Y-m-d H:i:s');
      print($now);
      $query = (mysqli_query($conn,"SELECT * FROM _assignment WHERE id_simulation = $id_simulation AND status = 1 AND pickup_timestamp < '".$now."'"));
      while ($row = mysqli_fetch_assoc($query)) {
            mysqli_query($conn, "UPDATE _assignment SET status = 2 WHERE id = ".$row['id']);
      }
}
else if(isset($_POST['setFinishOrder'])){
      $id_simulation = $_POST['id_simulation'];
      $result_array = Array();
      $now = date('Y-m-d H:i:s');
      $query = (mysqli_query($conn,"SELECT a.id,gd.id_driver, gp.id_passenger, a.id_generate_driver, a.id_generate_passenger 
            FROM _assignment a 
            JOIN _generate_drivers gd ON gd.id = a.id_generate_driver
            JOIN _generate_passengers gp ON gp.id = a.id_generate_passenger 
            WHERE a.id_simulation = $id_simulation AND a.status = 2 AND a.arrived_timestamp < '".$now."'"));
      while ($row = mysqli_fetch_assoc($query)) {
            mysqli_query($conn, "UPDATE _assignment SET status = 3 WHERE id = ".$row['id']);
            mysqli_query($conn, "UPDATE _driver SET status = 0 WHERE id_driver = ".$row['id_driver']);
            mysqli_query($conn, "UPDATE _passenger SET status = 0 WHERE id_passenger = ".$row['id_passenger']);
            mysqli_query($conn, "UPDATE _generate_passengers SET status = 0 WHERE id = ".$row['id_generate_passenger']);
            mysqli_query($conn, "UPDATE _generate_drivers SET status = 0 WHERE id = ".$row['id_generate_driver']);            
      }
}
else if(isset($_POST['loadCheckboxArea'])){
      $query = (mysqli_query($conn,"SELECT * FROM _area WHERE is_active = 1"));
      while ($row = mysqli_fetch_assoc($query)) {
            echo '
            <input class="form-check-input" type="checkbox" value="'.$row['id'].'" name="checkarea[]">
            <label class="form-check-label" for="flexCheckDefault">
            '.$row['name'].'
            </label><br>';
      }
}
else if(isset($_POST['loadFaktor'])){
      $query = (mysqli_query($conn,"SELECT * FROM _factor WHERE is_active = 1"));
      while ($row = mysqli_fetch_assoc($query)) {
            echo '
            <li class="list-group-item">'.$row['name'].'</li>';
      }
}
else if(isset($_POST['loadRFMCheckboxArea'])){
      $query = (mysqli_query($conn,"SELECT * FROM _area_used au INNER JOIN _area a ON au.id_area = a.id WHERE au.id_simulation =". $_POST['id_simulation']));
      while ($row = mysqli_fetch_assoc($query)) {
            echo '
            <div name="areas">
            <label class="form-check-input" for="flexCheckDefault" id="' . $row['id'] . '">
            '.$row['name'].'
            </label></div><br>';
      }
}
else if(isset($_POST['loadRFMFaktor'])){
      $query = (mysqli_query($conn,"SELECT * FROM `_factor_used` fu inner join _factor f on fu.id_factor = f.id where fu.id_simulation=". $_POST['id_simulation'] . " and f.id!=5 and f.id!=8"));
      while ($row = mysqli_fetch_assoc($query)) {
            echo '
            <input class="form-check-input" type="checkbox" name="checkbox[]" value="'. $row['name'] . '" checked>
            <label class="form-check-label " for="flexCheckDefault">
            '. $row['name'] . '
            </label>
            <input type="number" min ="0" max="100" style="width: 70%" class="form-check-label form-control" id="'. $row['name'] . 'prec" name="'. $row['name'] . 'prec" placeholder="'. $row['precentage'] . '" required><br>
            ';
      }
      echo '<input class="form-check-input" type="checkbox" name="checkbox[]" value="rfm_driver" checked>
      <label class="form-check-label " for="flexCheckDefault">
      RFM Score Driver
      </label>
      <input type="number" min ="0" max="100" style="width: 70%" class="form-check-label form-control" id="rfmd_prec" name="rfmd_prec" placeholder="0" required><br>
      <input class="form-check-input" type="checkbox" name="checkbox[]" value="rfm_pass" checked>
      <label class="form-check-label " for="flexCheckDefault">
      RFM Score Passenger
      </label>
      <input type="number" min ="0" max="100" style="width: 70%" class="form-check-label form-control" id="rfmp_prec" name="rfmp_prec" placeholder="0" required><br>
      ';
}

else if(isset($_POST['loadComparisonFaktor'])){
      $query = (mysqli_query($conn,"SELECT * FROM `_factor_used` fu inner join _factor f on fu.id_factor = f.id where fu.id_simulation=". $_POST['id_simulation']));
      while ($row = mysqli_fetch_assoc($query)) {

            echo '
            <input class="form-check-input" type="checkbox" name="checkbox[]" value="'. $row['name'] . '" checked>
            <label class="form-check-label " for="flexCheckDefault">
            '. $row['name'] . '
            </label>
            <input type="number" min ="0" max="100" style="width: 70%" class="form-check-label form-control" id="'. $row['name'] . 'prec" name="'. $row['name'] . 'prec" value="'. $row['precentage'] . '" required><br>
            ';
            if($row['input_goal']!=null && $row['id_factor'] == 4){
                  echo '<select class="form-control" style="width: 70%" id="goal_totaldist" name="goal_totaldist">';
                  if($row['input_goal'] == 0) echo '<option value="0">Min</option>';
                  else echo '<option value="1">Max</option>';
                  echo '</select>';
            }
            if($row['input_goal']!=null && $row['id_factor'] == 2){
                  echo '<select class="form-control" style="width: 70%" id="goal_totaltrip" name="goal_totaltrip">';
                  if($row['input_goal'] == 0) echo '<option value="0">Min</option>';
                  else echo '<option value="1">Max</option>';
                  echo '</select>';
            }
      }
}

else if(isset($_POST['prev'])){
      $id = $_POST['id'];
      $batch=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_batch, batch_num FROM _batch 
            WHERE id_simulation = $id AND is_current_batch = 1 ORDER BY batch_num DESC LIMIT 1"));
      $id_batch = $batch['id_batch'];
      $prev_batchnum = $batch['batch_num']-1;

      $inactivateBatch = mysqli_query($conn, "UPDATE _batch SET is_current_batch = 0 WHERE id_batch = $id_batch");
      $updateCurrentBatch = mysqli_query($conn, "UPDATE _batch SET is_current_batch = 1 WHERE id_simulation = $id AND batch_num = $prev_batchnum");

      // $deleteGenDrivers = mysqli_query($conn, "SELECT * FROM _generate_drivers WHERE id_simulation = $id AND id_batch = ".$batch['id_batch']);
      // $deleteGenPassengers = mysqli_query($conn, "SELECT * FROM _generate_passengers WHERE id_simulation = $id AND id_batch = ".$batch['id_batch']);

      // while ($row = mysqli_fetch_assoc($deleteGenDrivers)) {
      //       mysqli_query($conn, "UPDATE FROM _driver SET status = 0 WHERE id_driver = ".$row['id_driver']);
      //       mysqli_query($conn,"DELETE FROM _assignment WHERE id_simulation = $id AND id_batch = $id_batch AND id_generate_driver = ".$row['id']);
      // }
      // while ($row = mysqli_fetch_assoc($deleteGenPassengers)) {
      //       mysqli_query($conn, "UPDATE FROM _passenger SET status = 0 WHERE id_passenger = ".$row['id_passenger']);
      // }

      // mysqli_query($conn,"DELETE FROM _generate_drivers WHERE id_simulation = $id AND id_batch = ".$batch['id_batch']);
      // mysqli_query($conn,"DELETE FROM _generate_passengers WHERE id_simulation = $id AND id_batch = ".$batch['id_batch']);
      // mysqli_query($conn,"DELETE FROM _batch WHERE id_simulation = $id AND id_batch = ".$batch['id_batch']);
}
else if(isset($_POST['next'])){
      $id = $_POST['id'];
      $batch=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_batch, batch_num FROM _batch 
            WHERE id_simulation = $id AND is_current_batch = 1 ORDER BY batch_num DESC LIMIT 1"));
      $id_batch = $batch['id_batch'];
      $next_batchnum = $batch['batch_num']+1;

      $cek_batch = mysqli_num_rows(mysqli_query($conn, "SELECT id_batch FROM _batch WHERE id_simulation = $id AND batch_num = $next_batchnum"));
      if($cek_batch > 0){
            $inactivateBatch = mysqli_query($conn, "UPDATE _batch SET is_current_batch = 0 WHERE id_batch = $id_batch");
            $updateCurrentBatch = mysqli_query($conn, "UPDATE _batch SET is_current_batch = 1 WHERE id_simulation = $id AND batch_num = $next_batchnum");
            echo 'isNotNew';
      }
      else echo 'isNew';
}
else if(isset($_POST['endSimulation'])){
      // $simulation = mysqli_query($conn,"UPDATE _simulation SET status=0 where id=". $_POST['id_simulation']));
      $id_simulation = $_POST['id_simulation'];
      $query = (mysqli_query($conn,"SELECT * FROM _assignment WHERE id_simulation = $id_simulation AND status != 3"));
      while ($row = mysqli_fetch_assoc($query)) {
            mysqli_query($conn, "UPDATE _assignment SET status = 3 WHERE id = ".$row['id']);                      
      }
      mysqli_query($conn, "UPDATE _passenger SET status = 0");
      mysqli_query($conn, "UPDATE _driver SET status = 0"); 
      $simulation = (mysqli_query($conn, "UPDATE _simulation SET status=0 where id=$id_simulation"));
}
?>