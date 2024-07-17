<?php
function INSERT_data($username, $distances, $angles, $connection)
{
    $distancesJson = json_encode($distances);
    $anglesJson = json_encode($angles);
     try
    {
    $put=$connection->prepare("INSERT INTO face_info (username,distances,angeles) VALUES (:username,:dictance,:angeles)");
    $put->bindParam(':username',$username);
    $put->bindParam(':dictance',$distancesJson);
    $put->bindParam(':angeles',$anglesJson);
    $put->execute();
    }
    catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function SELECT_user($connection,$username){
     try
    {
    $get= $connection->prepare("SELECT username from face_info WHERE username =:user ");
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
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $distances = $data['distances'];
    $angles = $data['angles'];

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
    $result=SELECT_user($conn,$username);
    
    if(count($result) == 0)//if username ok
    {
    INSERT_data($username, $distances, $angles, $conn);
    $responseData =
    [
    'success' => true,
    'message' => 'signup successful!', 
    'redirectUrl' => 'https://digi-ai.ir/project/code/login.php' 
    ];

    $responseJson = json_encode($responseData);
    header('Content-Type: application/json');
    http_response_code(200);
    echo $responseJson; 
    }
    else//if username not uniqe
    {
    $responseData =
    [
    'success' => false,
    'message' => 'signup not successful!', 
    'redirectUrl' => 'https://digi-ai.ir/project/code/signup.php' 
    ];

    $responseJson = json_encode($responseData);
    header('Content-Type: application/json');
    http_response_code(200);
    echo $responseJson;    
    }
}
?>