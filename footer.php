<div class="col-12 px-0 text-center border-top border-dark" id="bottom">
    <p class="text-light mt-1">Connnect With Us</p>
    <div class="col-12 col-md-6 offset-md-3">
        <h5>
            <a href="https://web.facebook.com/faithcenterimus/" class="fab fa-facebook text-dark text-decoration-none"> @faithcenterimus</a>
        </h5>
    </div>
    <div class="col-12 col-md-6 offset-md-3">
        <h5>
            <a href="https://faithcenterimus.wixsite.com/faithcenterimus" class="fa fa-globe text-dark text-decoration-none"> Official Website</a>
        </h5>
    </div>
    <div class="col-12 col-md-6 offset-md-3">
        <h5>
            <a href="" class="fa fa-phone text-dark text-decoration-none"> Tel: 63.46.471.3516</a>
        </h5>
    </div>
    <div class="col-12 col-md-6 offset-md-3">
        <h5>
            <a href="" class="fa fa-envelope-square text-dark text-decoration-none mb-3"> faithcenterimus@yahoo.com</a>
        </h5>
    </div>
    
</div>


        </div>
    </body>
    <script type="text/javascript"> 
        $('#popup').on('hidden.bs.modal',function(){
            $('#popup').find('span').empty();
            $.ajax({
                url: "clearSessionMessage.php",
                success: function () {
                }
            });
        });
        
    <?php if(isset($_SESSION['uname']))    
        echo "$('form').keydown(function (e) {if (e.keyCode == 13) {e.preventDefault();return false;}});";
    ?>        
        $('#newFirstName').on('input',function (){
                fname=$(this).val().replace(' ','.').toLowerCase();
                lname=$('#newLastname').val().replace(' ','.').toLowerCase();
                autoUserAndPass=fname+'.'+lname; 
                $('#newPassword').val(autoUserAndPass);
                $('#newUsername').val(autoUserAndPass);
            });
            $('#newLastname').on('input',function (){
                fname=$('#newFirstName').val().replace(' ','.').toLowerCase();
                lname=$(this).val().replace(' ','.').toLowerCase();
                autoUserAndPass=fname+'.'+lname; 
                $('#newPassword').val(autoUserAndPass);
                $('#newUsername').val(autoUserAndPass);
            });
            $('#newBDate').on('input',function(){
                dob = new Date($(this).val());
                today = new Date();
                age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                $('#newAge').val(age);
            });
        
        $('#CollapseDiv').on('shown.bs.collapse',function(){
            $('#FirstName').on('input',function (){
                fname=$(this).val().replace(' ','.').toLowerCase();
                lname=$('#Lastname').val().replace(' ','.').toLowerCase();
                autoUserAndPass=fname+'.'+lname; 
                $('#Password').val(autoUserAndPass);
                $('#Username').val(autoUserAndPass);
            });
            $('#Lastname').on('input',function (){
                fname=$('#FirstName').val().replace(' ','.').toLowerCase();
                lname=$(this).val().replace(' ','.').toLowerCase();
                autoUserAndPass=fname+'.'+lname; 
                $('#Password').val(autoUserAndPass);
                $('#Username').val(autoUserAndPass);
            });
            $('#NewBDate').on('input',function(){
                dob = new Date($(this).val());
                today = new Date();
                age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                $('#NewAge').val(age);
            });
        });
        
        $('#BDate').on('input',function(){
            dob = new Date($(this).val());
            today = new Date();
            age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
            $('#Age').val(age);
        });
        
        $('#SelectSeatTimeBtn').find('button').each(function(index){
            $(this).on('click',function(){
                target='.collapseform'+index;
                $(this).removeClass('fa-caret-up').addClass('btn-success fa-caret-up');
                $(this).siblings('.btn-success').removeClass('btn-success fa-caret-up').addClass('fa-caret-down');
                $('#SeatSelection').find(" div.collapse,.show").removeClass('show');
                //$('#SeatSelection').find(" div.collapse,.show").collapse('hide');
                
                $(target).addClass('show');
                //$(target).collapse('show');
                
                
                
            });
        });
        
        $('#showForm').on('click', function(){
            $(this).toggleClass('fa-caret-up');
        });
        
        $('#updateTable').find('input').each(function(){
            $(this).on('input',function(){
                $('#updateTable').find('button:disabled').css('cursor', 'pointer');
                $('#updateTable').find('button:disabled').prop('disabled',false);
            });
        });

        $('#defaultSettingsTbl').find('input').each(function(){
            $(this).on('input',function(){
                $('#defaultSettingsTbl').find('button:disabled').css('cursor', 'pointer');
                $('#defaultSettingsTbl').find('button:disabled').prop('disabled',false);
            });
        });

        $('#currWeekSettingsTbl').find('input').each(function(){
            $(this).on('input',function(){
                $('#currWeekSettingsTbl').find('button:disabled').css('cursor', 'pointer');
                $('#currWeekSettingsTbl').find('button:disabled').prop('disabled',false);
            });
        });

        $('#toggleShowPass').on('click', function(){
            $(this).find('i').toggleClass('fa-eye-slash');
            if($('#password').attr('type')=='password'){
                $('#password').prop('type', 'text');
                $(this).find('i').prop('title', 'Hide');
            }else{
                $('#password').prop('type', 'password');
                $(this).find('i').prop('title', 'Show');
            }
        });

        $('#searchcAcc').on('input', function(){
            $('#searchResults').empty();
            var search = $(this).val();
            if(search != ""){
                $.ajax({
                    type: "POST",
                    url: 'searchAccount.php',
                    data: {searchVerified: search},
                    success: function (data) {
                        $('#searchResults').append(data);  
                    }
                });
            }
        });

        $('#searchcAcc').on('blur', function(){
            if(!$('#searchResults').is(':focus')){
                $('#searchResults').empty();
            }
        });

        $('#searchcAcc2').on('input',function(){
            $('#searchResults2').empty();
            var search = $(this).val();
            if(search != ""){
                $.ajax({
                    type: "POST",
                    url: 'searchAccount.php',
                    data: {searchUnverified: search},
                    success: function (data) {
                        $('#searchResults2').append(data);  
                    }
                });
            }
        });

        $('#searchcAcc2').on('blur',function(){
            if(!$('#searchResults2').is(':focus')){
                $('#searchResults2').empty();
            }
        });


    </script>
</html>