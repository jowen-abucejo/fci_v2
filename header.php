<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title?></title>
        <link rel="stylesheet" type="text/css" href="lib/bootstrap-4.4.1/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="lib/css/all.css">
        <link rel="stylesheet" type="text/css" href="lib/css/custom.css">
        <script src="lib/jquery-3.4.1.js"></script>
        <script src="lib/bootstrap-4.4.1/bootstrap.bundle.min.js"></script>    
    </head>
    <body>   
        <div class="w-100 h-100 px-0 mx-0">
            <?php include 'menu.php';?>
            <div class="modal fade mt-5" id="popup" >
                <div class="modal-dialog" >
                    <div class="modal-content" >    
                        <div class="modal-header align-middle text-center">
                            <h5 class="modal-title w-100">
                                <?php echo (isset($_SESSION['msg']))?$_SESSION['msg']:''; ?>   
                                <?php echo (isset($_POST['del_msg']))?$_POST['del_msg']:''; ?> 
                            </h5>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
