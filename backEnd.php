<?php
//connection variables
$db_site = "sql1.njit.edu";
$db_user = "db387";
$db_pwd = "cNs9qKSwv";
$db = "db387";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);















//Project Code
//connect to database or display error
$conn = mysqli_connect($db_site, $db_user, $db_pwd, $db);
if (mysqli_connect_errno()){
  echo "Failed to connect to: " . mysql_connect_error();
}

//Login permissions
if (isset($_POST["ucid"]) AND isset($_POST["password"])){
  $user=$pwd=$perm='';
  $user = $_POST["ucid"];
  $pwd = $_POST["password"];
  //get user info from database
  $sql = "SELECT * from Users WHERE user='$user'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $pass = $row['pass'];
  $perm = $row['Perms'];
  //verify if password is correct databases hashed password
  $pwd_check = password_verify($pwd, $pass);
  //responds with no permissions
  if ($pwd_check == false){
    $json = '{"Perms":"No Permissions"}';
    echo $json;
  }
  else{
    //responds with appropriate permissions
    $json = '{"Perms":"'.$perm.'"}';
    echo $json;
  }
}

//Step 1
//Questions are being made/shown
if (isset($_POST["Question"])){
  $Questionflag = $_POST["Question"];
  //check if flag is true for recieve or false for give
  //sending all questions in db
  if ($Questionflag == "false"){
    $result = mysqli_query($conn, "SELECT * from Questions");
    if (mysqli_num_rows($result) > 0){
      $theString = '{';
      //add each question's information to string in json format
      while ($row = mysqli_fetch_assoc($result)){
        $theString .= '"'.$row['id'].'":["'.$row['FuncDiff'].'","'.$row['FuncTopic'].'","'.$row['FuncName'].'","'.$row['FuncDescrip'].'","'.$row['FuncParam'].'"'.'],';
      }
      $theString = substr($theString, 0, -1);
      $theString .= "}";
      echo $theString;
    }
  }
  //Receive a question information
  else{
    $FuncName = $_POST["FuncName"];
    $FuncDescrip = $_POST["FuncDescrip"];
    $FuncParam = $_POST["FuncParam"];
    $FuncDiff = $_POST["FuncDiff"];
    $FuncTopic = $_POST["FuncTopic"];
    $FuncCons = $_POST["FuncCons"];
    $CaseOneI = str_replace('\'', '"', $_POST["CaseOneI"]);
    $CaseOneO = $_POST["CaseOneO"];
    $CaseTwoI = str_replace('\'', '"', $_POST["CaseTwoI"]);
    $CaseTwoO = $_POST["CaseTwoO"];
    $CaseThreeI=$CaseThreeO=$CaseFourI=$CaseFourO=$CaseFiveI=$CaseFiveO=$CaseSixI=$CaseSixO='NA';
    if (isset($_POST["CaseThreeI"])){
      $CaseThreeI = str_replace('\'', '"', $_POST["CaseThreeI"]);
      $CaseThreeO = $_POST["CaseThreeO"];
    }
    if (isset($_POST["CaseFourI"])){
      $CaseFourI = str_replace('\'', '"', $_POST["CaseFourI"]);
      $CaseFourO = $_POST["CaseFourO"];
    }
    if (isset($_POST["CaseFiveI"])){
      $CaseFiveI = str_replace('\'', '"', $_POST["CaseFiveI"]);
      $CaseFiveO = $_POST["CaseFiveO"];
    }
    if (isset($_POST["CaseSixI"])){
      $CaseSixI = str_replace('\'', '"', $_POST["CaseSixI"]);
      $CaseSixO = $_POST["CaseSixO"];
    }
    $sql = "INSERT INTO Questions
    (`id`, `FuncName`, `FuncDescrip`, `FuncParam`, `FuncDiff`,
      `FuncTopic`, `FuncCons`, `CaseOneI`, `CaseOneO`, `CaseTwoI`, `CaseTwoO`, `CaseThreeI`,
      `CaseThreeO`, `CaseFourI`, `CaseFourO`, `CaseFiveI`, `CaseFiveO`, `CaseSixI`, `CaseSixO`)
      VALUES (NULL,'$FuncName','$FuncDescrip','$FuncParam','$FuncDiff','$FuncTopic','$FuncCons',
      '$CaseOneI','$CaseOneO','$CaseTwoI','$CaseTwoO', '$CaseThreeI', '$CaseThreeO',
      '$CaseFourI', '$CaseFourO', '$CaseFiveI', '$CaseFiveO', '$CaseSixI', '$CaseSixO')";
    mysqli_query($conn, $sql);
    echo $sql;
  }
}

//Step 2
//Making an Exam/Showing potential questions for an exam
if (isset($_POST["Exam"])){
  $Examflag = $_POST["Exam"];
  //sending information about the specific question
  if ($Examflag == "false"){
    $tempID = $_POST['ID'];
    $result = mysqli_query($conn, "SELECT * from Questions WHERE id = '$tempID'");
    if (mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_assoc($result);
      $theString = '{';
      //add each question's information to string in json format
      $theString .= '"'.$row['id'].'":["'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"'.']';
      $theString .= "}";
      echo $theString;
    }
  }
  else{
    //Recieving an exam
    $ID = json_decode($_POST["ID"]);
    $QOne=$ID[0];
    $VOne=$ID[1];
    $QTwo=$ID[2];
    $VTwo=$ID[3];
    $QThree=$QFour=$QFive=$VThree=$VFour=$VFive='0';
    if (array_key_exists("4", $ID)){
      $QThree=$ID[4];
      $VThree=$ID[5];
    }
    if (array_key_exists("6", $ID)){
      $QFour=$ID[6];
      $VFour=$ID[7];
    }
    if (array_key_exists("8", $ID)){
      $QFive=$ID[8];
      $VFive=$ID[9];
    }
    $totalPoints=$_POST["TotalPoints"];
    $sql = "INSERT INTO EmptyExam (`ExamID`, `Q1`, `Q2`, `Q3`, `Q4`, `Q5`,
      `V1`, `V2`, `V3`, `V4`, `V5`, `TotalPoints`, `DATE`)
      VALUES (NULL, '$QOne', '$QTwo', '$QThree', '$QFour', '$QFive', '$VOne',
         '$VTwo', '$VThree', '$VFour', '$VFive', '$totalPoints', CURRENT_TIMESTAMP())";
    mysqli_query($conn, $sql);
    echo "Received";
  }
}

//Step 3
//student is taking the exam
if (isset($_POST["StudentExam"])){
  $sExamflag = $_POST["StudentExam"];
  //Sending exam information for student
  if ($sExamflag == "false"){
    $SID=$_POST["UCID"];
    $theString = '{"1":["';
    $ExamResult = mysqli_query($conn, "SELECT * FROM EmptyExam ORDER BY DATE DESC LIMIT 1");
    $examRow = mysqli_fetch_assoc($ExamResult);
    if (isset($_POST["NumQuestions"])){
      $i=0;
      for ($j=1; $j<6; $j++){
        if ($examRow["Q".$j]!=0){
          $i++;
        }
      }
      echo $i;
    }
    else{
      $qPlace=$_POST["QuestionNum"];
      $Qnum = "Q".$qPlace;
      $QID=$examRow[$Qnum];
      //end of exam questions
      if ($QID==0){
        $theString .= 'End"]}';
        echo $theString;
      }
      //more exam questions
      else{
        $qR=mysqli_query($conn, "SELECT * FROM Questions WHERE id = $QID");
        if (mysqli_num_rows($qR) > 0 && $qPlace=='1'){
          $row = mysqli_fetch_assoc($qR);
          $theString .= $examRow['V1'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"]}';
          echo $theString;
        }
        elseif (mysqli_num_rows($qR) > 0 && $qPlace=='2'){
          $row = mysqli_fetch_assoc($qR);
          $theString .= $examRow['V2'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"]}';
          echo $theString;
        }
        elseif(mysqli_num_rows($qR) > 0 && $qPlace=='3'){
          $row = mysqli_fetch_assoc($qR);
          $theString .= $examRow['V3'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"]}';
          echo $theString;
        }
        elseif(mysqli_num_rows($qR) > 0 && $qPlace=='4'){
          $row = mysqli_fetch_assoc($qR);
          $theString .= $examRow['V4'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"]}';
          echo $theString;
        }
        elseif(mysqli_num_rows($qR) > 0 && $qPlace=='5'){
          $row = mysqli_fetch_assoc($qR);
          $theString .= $examRow['V5'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'"]}';
          echo $theString;
        }
      }
    }
  }
  else{
    $SID=$_POST["UCID"];
    $examSearch = mysqli_query($conn, "SELECT * FROM EmptyExam ORDER BY DATE DESC LIMIT 1");
    $searchRow = mysqli_fetch_assoc($examSearch);
    if (isset($_POST["RONE"])){
      //New entry to FilledExam
      $sResponse = $_POST["RONE"];
      $ExamID=$searchRow["ExamID"];
      $Q1 = $searchRow['Q1'];
      $V1 = $searchRow['V1'];
      $TotalPoints = $searchRow['TotalPoints'];
      $sql = "INSERT INTO FilledExam (`ExamID`, `StudentID`, `Q1`, `V1`, `R1`, `TotalPoints`, `DATE`)
      VALUES ('$ExamID', '$SID', '$Q1', '$V1', '$sResponse', '$TotalPoints', CURRENT_TIMESTAMP())";
      mysqli_query($conn, $sql);
      echo $sql;
    }
    else{
      //Updating most recent
      $sResponse = '';
      $Qnum=$QV=$QR='';
      if (isset($_POST["RTWO"])){
        $sResponse = $_POST["RTWO"];
        $Qnum="Q2";
        $QV = "V2";
        $QR= "R2";
      }
      if (isset($_POST["RTHREE"])){
        $sResponse = $_POST["RTHREE"];
        $Qnum="Q3";
        $QV = "V3";
        $QR= "R3";
      }
      if (isset($_POST["RFOUR"])){
        $sResponse = $_POST["RFOUR"];
        $Qnum="Q4";
        $QV = "V4";
        $QR= "R4";
      }
      if (isset($_POST["RFive"])){
        $sResponse = $_POST["RFive"];
        $Qnum="Q5";
        $QV = "V5";
        $QR= "R5";
      }
      $sql = "UPDATE FilledExam SET $Qnum = $searchRow[$Qnum], $QV = $searchRow[$QV], $QR = '$sResponse' WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1";
      mysqli_query($conn, $sql);
      echo $sResponse;
    }
  }
}

//Step 4a
//Taken Exam is being autograded by middle
if (isset($_POST["GradingExam"])){
  $Gradeflag = $_POST["GradingExam"];
  //Sending exam information for most recent taken exam by student to middle
  if ($Gradeflag == "false"){
    $theString='';
    //Post taken exam information
    $ExamResult = mysqli_query($conn, "SELECT * FROM FilledExam ORDER BY DATE DESC LIMIT 1");
    $temp = mysqli_fetch_assoc($ExamResult);
    $Q1ID = $temp["Q1"];
    $Q2ID = $temp["Q2"];
    $Q3ID = $temp["Q3"];
    $Q4ID = $temp["Q4"];
    $Q5ID = $temp["Q5"];
    $Q1Result = mysqli_query($conn, "SELECT * FROM Questions WHERE id='$Q1ID'");
    $Q1R = mysqli_fetch_assoc($Q1Result);
    $theString .= $Q1R["FuncName"].'%'.$Q1R["FuncParam"].'%'.strtolower($Q1R["FuncCons"]).'%'.$temp["R1"].'%';
    $theString .= $temp["V1"].'%'.$Q1R["CaseOneI"].'%'.$Q1R["CaseOneO"].'%'.$Q1R["CaseTwoI"].'%'.$Q1R["CaseTwoO"];
    $theString .= '%'.$Q1R["CaseThreeI"].'%'.$Q1R["CaseThreeO"].'%'.$Q1R["CaseFourI"].'%'.$Q1R["CaseFourO"].'%'.$Q1R["CaseFiveI"].'%'.$Q1R["CaseFiveO"].'%'.$Q1R["CaseSixI"].'%'.$Q1R["CaseSixO"];
    //Question 2 information
    $Q2Result = mysqli_query($conn, "SELECT * FROM Questions WHERE id='$Q2ID'");
    $Q2R = mysqli_fetch_assoc($Q2Result);
    $theString .= '%'.$Q2R["FuncName"].'%'.$Q2R["FuncParam"].'%'.strtolower($Q2R["FuncCons"]).'%'.$temp["R2"].'%';
    $theString .= $temp["V2"].'%'.$Q2R["CaseOneI"].'%'.$Q2R["CaseOneO"].'%'.$Q2R["CaseTwoI"].'%'.$Q2R["CaseTwoO"];
    $theString .= '%'.$Q2R["CaseThreeI"].'%'.$Q2R["CaseThreeO"].'%'.$Q2R["CaseFourI"].'%'.$Q2R["CaseFourO"].'%'.$Q2R["CaseFiveI"].'%'.$Q2R["CaseFiveO"].'%'.$Q2R["CaseSixI"].'%'.$Q2R["CaseSixO"];
    //Question 3 information
    if ($Q3ID != 0){
      $Q3Result = mysqli_query($conn, "SELECT * FROM Questions WHERE id='$Q3ID'");
      $Q3R = mysqli_fetch_assoc($Q3Result);
      $theString .= '%'.$Q3R["FuncName"].'%'.$Q3R["FuncParam"].'%'.strtolower($Q3R["FuncCons"]).'%'.$temp["R3"].'%';
      $theString .= $temp["V3"].'%'.$Q3R["CaseOneI"].'%'.$Q3R["CaseOneO"].'%'.$Q3R["CaseTwoI"].'%'.$Q3R["CaseTwoO"];
      $theString .= '%'.$Q3R["CaseThreeI"].'%'.$Q3R["CaseThreeO"].'%'.$Q3R["CaseFourI"].'%'.$Q3R["CaseFourO"].'%'.$Q3R["CaseFiveI"].'%'.$Q3R["CaseFiveO"].'%'.$Q3R["CaseSixI"].'%'.$Q3R["CaseSixO"];
    }
    //Question 4 information
    if ($Q4ID != 0){
      $Q4Result = mysqli_query($conn, "SELECT * FROM Questions WHERE id='$Q4ID'");
      $Q4R = mysqli_fetch_assoc($Q4Result);
      $theString .= '%'.$Q4R["FuncName"].'%'.$Q4R["FuncParam"].'%'.strtolower($Q4R["FuncCons"]).'%'.$temp["R4"].'%';
      $theString .= $temp["V4"].'%'.$Q4R["CaseOneI"].'%'.$Q4R["CaseOneO"].'%'.$Q4R["CaseTwoI"].'%'.$Q4R["CaseTwoO"];
      $theString .= '%'.$Q4R["CaseThreeI"].'%'.$Q4R["CaseThreeO"].'%'.$Q4R["CaseFourI"].'%'.$Q4R["CaseFourO"].'%'.$Q4R["CaseFiveI"].'%'.$Q4R["CaseFiveO"].'%'.$Q4R["CaseSixI"].'%'.$Q4R["CaseSixO"];
    }
    //Question 5 information
    if ($Q5ID != 0){
      $Q5Result = mysqli_query($conn, "SELECT * FROM Questions WHERE id='$Q5ID'");
      $Q5R = mysqli_fetch_assoc($Q5Result);
      $theString .= '%'.$Q5R["FuncName"].'%'.$Q5R["FuncParam"].'%'.strtolower($Q5R["FuncCons"]).'%'.$temp["R5"].'%';
      $theString .= $temp["V5"].'%'.$Q5R["CaseOneI"].'%'.$Q5R["CaseOneO"].'%'.$Q5R["CaseTwoI"].'%'.$Q5R["CaseTwoO"];
      $theString .= '%'.$Q5R["CaseThreeI"].'%'.$Q5R["CaseThreeO"].'%'.$Q5R["CaseFourI"].'%'.$Q5R["CaseFourO"].'%'.$Q5R["CaseFiveI"].'%'.$Q5R["CaseFiveO"].'%'.$Q5R["CaseSixI"].'%'.$Q5R["CaseSixO"];
    }
    echo $theString;
  }
  else{
    //Autograded Exam information received
    $Grade1=$Grade2=$Grade3=$Grade4=$Grade5=0;
    $com1=$com2=$com3=$com4=$com5='';
    $examResult = mysqli_query($conn, "SELECT * FROM FilledExam ORDER BY DATE DESC LIMIT 1");
    $temp = mysqli_fetch_assoc($examResult);
    $ExamID = $temp["ExamID"];
    $SID=$temp["StudentID"];
    $Q1 = $temp["Q1"];
    $Q2 = $temp["Q2"];
    $Q3 = $temp["Q3"];
    $Q4 = $temp["Q4"];
    $Q5 = $temp["Q5"];
    $R1 = $temp["R1"];
    $R2 = $temp["R2"];
    $R3 = $temp["R3"];
    $R4 = $temp["R4"];
    $R5 = $temp["R5"];
    $V1 = $temp["V1"];
    $V2 = $temp["V2"];
    $V3 = $temp["V3"];
    $V4 = $temp["V4"];
    $V5 = $temp["V5"];
    $TotalPoints = $temp["TotalPoints"];
    $Grade1 = $_POST["ZeroP"];
    $com1 = $_POST["ZeroC"];
    $Grade2 = $_POST["OneP"];
    $com2 = $_POST["OneC"];
    $Grade3 = $_POST["TwoP"];
    $com3 = $_POST["TwoC"];
    $Grade4 = $_POST["ThreeP"];
    $com4 = $_POST["ThreeC"];
    $Grade5 = $_POST["FourP"];
    $com5 = $_POST["FourC"];
    $Grade = $_POST["ExamGrade"];
    $sql = "INSERT INTO AutoExam (`ExamID`, `StudentID`, `Q1`, `Q2`, `Q3`, `Q4`, `Q5`,
      `R1`, `R2`, `R3`, `R4`, `R5`, `V1`, `V2`, `V3`, `V4`, `V5`, `TotalPoints`,
      `G1`, `G2`, `G3`, `G4`, `G5`, `Grade`, `Q1Com`, `Q2Com`, `Q3Com`, `Q4Com`, `Q5Com`, `DATE`)
      VALUES ('$ExamID', '$SID', '$Q1', '$Q2', '$Q3', '$Q4', '$Q5', '$R1', '$R2', '$R3',
         '$R4', '$R5', '$V1', '$V2', '$V3', '$V4', '$V5', '$TotalPoints',
         '$Grade1', '$Grade2', '$Grade3', '$Grade4', '$Grade5',
          '$Grade', '$com1', '$com2', '$com3', '$com4', '$com5', CURRENT_TIMESTAMP())";
    mysqli_query($conn, $sql);
    echo $sql;
  }
}

//Step 4b
//Autograded Exam is being reviewed by professor
if (isset($_POST["StaffGrade"])){
  $StaffGrade = $_POST["StaffGrade"];
  if ($StaffGrade == "false"){
    $SID=$_POST["UCID"];
    $theString = '{"1":["';
    $ExamResult = mysqli_query($conn, "SELECT * FROM AutoExam WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1");
    $examRow = mysqli_fetch_assoc($ExamResult);
    if (isset($_POST["NumQuestions"])){
      $i=0;
      for ($j=1; $j<6; $j++){
        if ($examRow["Q".$j]!=0){
          $i++;
        }
      }
      echo $i;
    }
    else{
      if (isset($_POST["StudentResponse"])){
        $Qnum = "R".$_POST["StudentResponse"];
        echo $examRow[$Qnum];
      }
      else{
        $qPlace=$_POST["QuestionNum"];
        $Qnum = "Q".$qPlace;
        $QID=$examRow[$Qnum];
        //end of exam questions

        if ($QID==0){
          $theString .= 'End"]}';
          echo $theString;
        }
        //more exam questions
        else{
          $qR=mysqli_query($conn, "SELECT * FROM Questions WHERE id = $QID");
          if (mysqli_num_rows($qR) > 0 && $qPlace=='1'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G1'].'","'.$examRow['V1'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'",'.$examRow['Q1Com'].']}';
            echo $theString;
          }
          elseif (mysqli_num_rows($qR) > 0 && $qPlace=='2'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G2'].'","'.$examRow['V2'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'",'.$examRow['Q2Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='3'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G3'].'","'.$examRow['V3'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'",'.$examRow['Q3Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='4'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G4'].'","'.$examRow['V4'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'",'.$examRow['Q4Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='5'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G5'].'","'.$examRow['V5'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'",'.$examRow['Q5Com'].']}';
            echo $theString;
          }
        }
      }
    }
  }
  else{
    $SID=$_POST["UCID"];
    $examSearch = mysqli_query($conn, "SELECT * FROM AutoExam WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1");
    $searchRow = mysqli_fetch_assoc($examSearch);
    $qPlace=$_POST["QuestionNum"];
    $Grade=$_POST["Grade"];
    $ProfCom = $_POST["ProfComm"];
    $Comments=json_decode($_POST["Comments"]);
    $Comment = '["';
    foreach ($Comments as $token){
      $Comment .= $token.'","';
    }
    $Comment = substr($Comment, 0, -2);
    $Comment .= ']';
    if ($qPlace==1){
      //New entry to FinishedExam
      $ExamID=$searchRow["ExamID"];
      $Q1 = $searchRow['Q1'];
      $V1 = $searchRow['V1'];
      $R1 = $searchRow['R1'];
      $TotalPoints = $searchRow['TotalPoints'];
      $sql = "INSERT INTO FinishedExam (`ExamID`, `StudentID`, `Q1`, `G1`, `V1`, `R1`, `Grade`, `TotalPoints`, `Q1Com`, `Prof1`, `DATE`)
      VALUES ('$ExamID', '$SID', '$Q1', '$Grade', '$V1', '$R1', '$Grade', '$TotalPoints', '$Comment', '$ProfCom', CURRENT_TIMESTAMP())";
      mysqli_query($conn, $sql);
      echo $sql;
    }
    else{
      //Updating most recent
      $examRecent = mysqli_query($conn, "SELECT * FROM FinishedExam WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1");
      $recentExam = mysqli_fetch_assoc($examRecent);
      $TGrade = $recentExam["Grade"] + $Grade;
      $Qnum = "Q".$qPlace;
      $QG = "G".$qPlace;
      $QV = "V".$qPlace;
      $QR = "R".$qPlace;
      $QCom = "Q".$qPlace."Com";
      $PCom = "Prof".$qPlace;
      $sql = "UPDATE FinishedExam SET $Qnum = '$searchRow[$Qnum]', $QG = '$Grade', $QV = '$searchRow[$QV]', $QR = '$searchRow[$QR]', Grade = '$TGrade', $QCom = '$Comment', $PCom = '$ProfCom' WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1";
      mysqli_query($conn, $sql);
      echo $sql;
    }
  }
}

//Step 5
//Student views their graded exam
if (isset($_POST["StudentGrade"])){
  $StudentReview = $_POST["StudentGrade"];
  if ($StudentReview == "false"){
    $SID=$_POST["UCID"];
    $theString = '{"1":["';
    $ExamResult = mysqli_query($conn, "SELECT * FROM FinishedExam WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1");
    $examRow = mysqli_fetch_assoc($ExamResult);
    if (isset($_POST["NumQuestions"])){
      $i=0;
      for ($j=1; $j<6; $j++){
        if ($examRow["Q".$j]!=0){
          $i++;
        }
      }
      echo $i;
    }
    else{
      if (isset($_POST["StudentResponse"])){
        $Qnum = "R".$_POST["StudentResponse"];
        echo $examRow[$Qnum];
      }
      else{
        $qPlace=$_POST["QuestionNum"];
        $Qnum = "Q".$qPlace;
        $QID=$examRow[$Qnum];
        //end of exam questions
        if ($QID==0){
          $theString .= 'End"]}';
          echo $theString;
        }
        //more exam questions
        else{
          $qR=mysqli_query($conn, "SELECT * FROM Questions WHERE id = $QID");
          if (mysqli_num_rows($qR) > 0 && $qPlace=='1'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G1'].'","'.$examRow['V1'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'","'.$examRow['Prof1'].'",'.$examRow['Q1Com'].']}';
            echo $theString;
          }
          elseif (mysqli_num_rows($qR) > 0 && $qPlace=='2'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G2'].'","'.$examRow['V2'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'","'.$examRow['Prof2'].'",'.$examRow['Q2Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='3'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G3'].'","'.$examRow['V3'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'","'.$examRow['Prof3'].'",'.$examRow['Q3Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='4'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G4'].'","'.$examRow['V4'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'","'.$examRow['Prof4'].'",'.$examRow['Q4Com'].']}';
            echo $theString;
          }
          elseif(mysqli_num_rows($qR) > 0 && $qPlace=='5'){
            $row = mysqli_fetch_assoc($qR);
            $theString .= $examRow['G5'].'","'.$examRow['V5'].'","'.$row['FuncName'].'","'.$row['FuncParam'].'","'.$row['FuncDescrip'].'","'.$examRow['Prof5'].'",'.$examRow['Q5Com'].']}';
            echo $theString;
          }
        }
      }
    }
  }
  else{
    if (isset($_POST["ShowGrade"])){
      $SID=$_POST["UCID"];
      $ExamResult = mysqli_query($conn, "SELECT * FROM FinishedExam WHERE StudentID = $SID ORDER BY DATE DESC LIMIT 1");
      $examRow = mysqli_fetch_assoc($ExamResult);
      $theString = '{"1":["'.$examRow["Grade"].'","'.$examRow["TotalPoints"].'",["'.$examRow["G1"].'","'.$examRow["V1"].'","'.$examRow["G2"].'","'.$examRow["V2"];
      if ($examRow["V3"]!=0){
        $theString .= '","'.$examRow["G3"].'","'.$examRow["V3"];
      }
      if ($examRow["V4"]!=0){
        $theString .= '","'.$examRow["G4"].'","'.$examRow["V4"];
      }
      if ($examRow["V5"]!=0){
        $theString .= '","'.$examRow["G5"].'","'.$examRow["V5"];
      }
      $theString .= '"]]}';
      echo $theString;
    }
  }
}
/*
*/
mysqli_close($conn);
?>
