<?php
session_start();
function UPDATE_data($username,$new_distances,$new_angeles,$connection)
{
     try
    {
    $get= $connection->prepare("UPDATE face_info SET distances=:new_distances,angeles=:new_angeles WHERE username =:user");
     $get->bindParam(':user',$username);
     $get->bindParam(':new_distances',$new_distances);
     $get->bindParam(':new_angeles',$new_angeles);
    $get->execute();
    }
     catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function SELECT_data($username,$connection){
 try
    {
    $get= $connection->prepare("SELECT distances,angeles from face_info WHERE username =:user");
    $get->bindParam(':user',$username);
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
function withinTolerance($newValues, $storedValues, $tolerance) {
    $true=0;
    for ($i = 0; $i < count($newValues); $i++) {
        if (abs($newValues[$i] - $storedValues[$i]) < $tolerance)
        {
            $true++;
        }
    }
    return $true;
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $distances = $data['distances'];
    $angles = $data['angles'];
    $tolerance_dis=0.4;
    $tolerance_ang=8;
    
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
    
   $result=SELECT_data($username, $conn);
   if(count($result)!=0){
   $storedDistances = json_decode($result[0]['distances']);
   $storedAngles = json_decode($result[0]['angeles']);
   
   error_log("SELECT data result: " . print_r($storedDistances, true));
   error_log("SELECT data result: " . print_r($storedAngles, true));
     
   $correct_distance=withinTolerance($distances, $storedDistances, $tolerance_dis);
   $correct_angel=withinTolerance($angles, $storedAngles, $tolerance_ang);
     
    if (($correct_distance+$correct_angel)>=8)
    {
     $_SESSION['username'] = $username;
      $responseData =
    [
    'success' => true,
    'correct_distance' => $correct_distance,
    'correct_angel' => $correct_angel,
    'message' => 'Login successful!', // Adjust based on processing
    'redirectUrl' => 'https://digi-ai.ir/project/index.php' // Optional redirect URL
    ];

    $responseJson = json_encode($responseData);
    header('Content-Type: application/json');
    http_response_code(200);
    echo $responseJson;
    }
    else
    {
    $responseData = 
    [
    'success' => false,
    'correct_distance' => $correct_distance,
    'correct_angel' => $correct_angel,
    'message' => 'Login not successful!', // Adjust based on processing
    'redirectUrl' => 'https://digi-ai.ir/project/code/tf_login.html' // Optional redirect URL
    ];
    $responseJson = json_encode($responseData);
    header('Content-Type: application/json');
    http_response_code(200);
    echo $responseJson;
    }
   }
   else
   {
       $responseData = 
    [
    'success' => false,
    'message' => 'username not found!', // Adjust based on processing
    'redirectUrl' => 'https://digi-ai.ir/tf/code/tf_login.html' // Optional redirect URL
    ];
    $responseJson = json_encode($responseData);
    header('Content-Type: application/json');
    http_response_code(200);
    echo $responseJson;
   }
}
?>