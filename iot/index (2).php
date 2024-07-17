<?php
function INSERT_pot($connection,$humidity)
{
    try
    {
    $put=$connection->prepare("INSERT INTO pot (data) VALUES (:data)");
    $put->bindParam(':data',$humidity);
    $put->execute();
    }
    catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function SELECT_pot_data($connection)
{
    try
    {
    $get= $connection->prepare("SELECT data from pot ORDER BY id DESC LIMIT $limit_select");
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result;
    }
    catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
    
    
}
function SELECT_planet_lw($connection)//lw = last_watering
{
    try
    {
    $get= $connection->prepare("SELECT last_watering FROM planet");
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
    
}
function SELECT_planet_dwm($connection)//dwm=day_watering_min
{
    try
    {
    $get= $connection->prepare("SELECT day_water_min FROM planet");
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
    
}
function SELECT_planet_period($connection)
{
    try
    {
    $get= $connection->prepare("SELECT period FROM planet");
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
    
}
function find_period($connection)
{
    $conn=$connection;
    $dwm=SELECT_planet_dwm($conn);//dwm = day_watering_min
    $periodes=[];
    switch ($dwm)
    {
        case 1:
            array_push($periodes,7);
            break;
         case 2:
            array_push($periodes,3,4);
            break;
         case 3:
            array_push($periodes,2,2,3);
            break;
         case 4:
            array_push($periodes,2,2,2,1);
            break;
         case 5:
            array_push($periodes,2,2,1,1,1);
            break;
         case 6:
            array_push($periodes,2,1,1,1,1,1);
            break;
         case 7:
            array_push($periodes,1,1,1,1,1,1,1);
            break;
    }
    $time=SELECT_planet_period($conn);
    return (($periodes[$time])*(24*60*60));
}
function SELECT_esp_gpio($connection,$name)
{
    try
    {
    $get= $connection->prepare("SELECT gpio FROM esp_gpio WHERE name= :name");
    $get->bindParam(':name', $name);
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function SELECT_planet_wm($connection)//wm = watering_min
{
  try
    {
    $get= $connection->prepare("SELECT water_min FROM planet");
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
       
}
function SELECT_pomp_data($connection,$name)//name of pomp
{
    try
    {
    $get= $connection->prepare("SELECT liter_per_min FROM pomp_data WHERE name=:name");
    $get->bindParam(':name', $name);
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result[0][0];
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
    
}
function uptime_pomp($connection,$name)//name of pomp
{
     $pomp=SELECT_pomp_data($connection,$name);
    $dwm=SELECT_planet_dwm($connection);
    $wm=SELECT_planet_wm($connection);
    return $result=(($wm/$dwm)*60)/$pomp;//result per second
}
function UPDATE_planet_lw($connection)
{
      $newTime= date('Y-m-d H:i:s');   
    try
    {
    $get= $connection->prepare("UPDATE planet SET last_watering=:newtime");
    $get->bindParam(':newtime', $newTime);
    $get->execute();
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function UPDATE_planet_period($connection)
{   
    $dwm=SELECT_planet_dwm($connection);
    $period=SELECT_planet_period($connection);
    if($period == ($dwm-1))
        $new_period=0;
    else
        $new_period=$period+1;
    
     try
    {
    $get= $connection->prepare("UPDATE planet SET period=:new_period");
    $get->bindParam(':new_period', $new_period);
    $get->execute();
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function DELETE_pot($connection)
{
     try
    {
    $get= $connection->prepare("DELETE FROM pot");
    $get->execute();
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
// function get_feedback($timeout)//second
// {
//      $start_time = time();
//      while ((time() - $start_time) < $timeout) 
//      {
//          if(isset($_POST['feedback'])) 
//          {
//             return $_POST['feedback'];
//          }
//         usleep(100000);//micro second
//      }
//      return -1;
// }
function now_lw($connection)//distance now to last_watering
{
        $last_watering=SELECT_planet_lw($connection);
        $currentTime = date('Y-m-d H:i:s');
        $current=strtotime($currentTime);
        $lw=strtotime($last_watering);//lw=last watering
        return ($current-$lw);
        
}
function send_data($connection)
{
    $uptime=uptime_pomp($connection,"dc:6V");//name of pomp
   $uptime_s =(string)$uptime;
    $send=array("status"=>1,"uptime_pomp"=>$uptime_s);
    return $send;
}
?>



<?php
const humidity_average=2800;
date_default_timezone_set('Asia/Tehran');
$myfile = fopen("LOG.txt", "a");
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $hum=$_POST['humidity'];
    $txt = "data recive in main: humidity = ".$hum.":".date('Y-m-d H:i:s')."\n";
    fwrite($myfile, $txt);
    
    //echo "<br>".$hum."<br>";//1
    
    $host="localhost";
    $user="digiai_first";
    $pass="Nima@228";
    $dbname="digiai_first";
    
    try
   {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
  echo "Error: " . $e->getMessage();
    }
    
    if($hum<=humidity_average)
    {
        $time=now_lw($conn);
        //echo "<br>".$time."<br>";//2
        $base_time=find_period($conn);
        //echo "<br>".$base_time."<br>";//3
         if($time>=$base_time)
          {
            $send=send_data($conn);
            //echo "<br>".$send."<br>";//4
            echo json_encode($send);
            $dataString = implode(", ", $send);
             $txt = "data send (watering) in main:  ".$dataString."  :".date('Y-m-d H:i:s')."\n";
             fwrite($myfile, $txt);
          }
         else//if time is not enough 
        {
            INSERT_pot($conn,$hum);
            $send=array("status"=>0);
            echo json_encode($send);
            $dataString = implode(", ", $send);
             $txt = "data send (time less) in main:  ".$dataString."  :".date('Y-m-d H:i:s')."\n";
             fwrite($myfile, $txt);
        }
    }
    else//if$hum>humidity_average
    {
        INSERT_pot($conn,$hum);
        $send=array("status"=>0);
        echo json_encode($send);
        $dataString = implode(", ", $send);
        $txt = "data send (hum bigger) in main:  " .$dataString."   :".date('Y-m-d H:i:s')."\n";
        fwrite($myfile, $txt);
        
    }
    
    
    $conn = null;
}
fclose($myfile);
?>


