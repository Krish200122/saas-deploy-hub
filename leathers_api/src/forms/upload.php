
 <?php
//Checking weather it is a valid request or not
 if(!empty($_FILES['file_attachment']['name'])){
    //Taking the files from input
    $file = $_FILES['file'];
    //Getting the file name of the uploaded file
    $fileName = $_FILES['file']['name'];
    //Getting the Temporary file name of the uploaded file
    $fileTempName = $_FILES['file']['tmp_name'];
    //Getting the file size of the uploaded file
    $fileSize = $_FILES['file']['size'];
    //getting the no. of error in uploading the file
    $fileError = $_FILES['file']['error'];
    //Getting the file type of the uploaded file
    $fileType = $_FILES['file']['type'];

    //Getting the file ext
    $fileExt = explode('.',$fileName);
    $fileActualExt = strtolower(end($fileExt));

    //Array of Allowed file type
    $allowedExt = array("jpg","jpeg","png","pdf","JPG","JPEG","PNG");

    //Checking, Is file extentation is in allowed extentation array
    if(in_array($fileActualExt, $allowedExt)){
        //Checking, Is there any file error
        if($fileError == 0){
            //Checking,The file size is bellow than the allowed file size
            if($fileSize < 10000000){
                //Creating a unique name for file
                $fileNemeNew = "kathir"."."."jpg";
                //File destination
                $fileDestination = '../assets/upload/'.$fileNemeNew;
                //function to move temp location to permanent location
                move_uploaded_file($fileTempName, $fileDestination);
                //Message after success
                echo "File Uploaded successfully";
            }else{
                //Message,If file size greater than allowed size
                echo "File Size Limit beyond acceptance";
            }
        }else{
            //Message, If there is some error
            echo "Something Went Wrong Please try again!";
        }
    }else{
        //Message,If this is not a valid file type
        echo "You can't upload this extention of file";
    }
}



?>