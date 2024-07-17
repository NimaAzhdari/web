<?php
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


?>
<?php
date_default_timezone_set('Asia/Tehran');
const full_humidity=3500;
$myfile = fopen("LOG.txt", "a");
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $feedback=$_POST['feedback'];
     $txt = "data recive in feedback: feedback=  ".$feedback."  :".date('Y-m-d H:i:s')."\n";
     fwrite($myfile, $txt);
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
    
    if($feedback > full_humidity)
                        {
                         UPDATE_planet_lw($conn);
                         UPDATE_planet_period($conn);
                         DELETE_pot($conn);
                         INSERT_pot($conn,$feedback);
                         $send=array("status"=>0);
                         echo json_encode($send);
                         $dataString = implode(", ", $send);
     $txt = "data send (feedback ok) in feedback:  ".$dataString.":".date('Y-m-d H:i:s')."\n";
     fwrite($myfile, $txt);
                        }
    else//if feedback uncorrect
    {
        $send=array("status"=>0);
        echo json_encode($send);
        $dataString = implode(", ", $send);
        $txt = "data send (feedback less) in feedback:  ".$dataString.":".date('Y-m-d H:i:s')."\n";
        fwrite($myfile, $txt);
    }


    $conn = null;
}
fclose($myfile);
?>