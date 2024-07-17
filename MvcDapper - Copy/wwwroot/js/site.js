
/*function toggleSignup(){
    document.getElementById("login-toggle").style.backgroundColor="#fff";
     document.getElementById("login-toggle").style.color="#222";
     document.getElementById("signup-toggle").style.backgroundColor="#57b846";
     document.getElementById("signup-toggle").style.color="#fff";
     document.getElementById("login-form").style.display="none";
     document.getElementById("signup-form").style.display="block";
  }
  
  function toggleLogin(){
     document.getElementById("login-toggle").style.backgroundColor="#57B846";
     document.getElementById("login-toggle").style.color="#fff";
     document.getElementById("signup-toggle").style.backgroundColor="#fff";
     document.getElementById("signup-toggle").style.color="#222";
     document.getElementById("signup-form").style.display="none";
     document.getElementById("login-form").style.display="block";
  }*/
     function validatelogin()
     {
     let user= document.forms["login"]["Username"].value;
     let pass= document.forms["login"]["Password"].value;
     let error=document.getElementById("error-login");
     if(user=="" || pass =="")
     {
     error.innerHTML ="موارد خالی را پر کنید";
     return false;
     }
     if(pass.length<8){
     error.innerHTML ="حداقل طول رمز هشت کاراکتر هست";
     return false;
     }
     }
     function validatesignup(){
     let phone= document.forms["signup"]["Phone"].value;  
     let user= document.forms["signup"]["Username"].value;  
     let pass= document.forms["signup"]["Password"].value;  
     let pass_2= document.forms["signup"]["signup-pass_2"].value;  
     let error=document.getElementById("error-signup"); 
     if(user=="" || pass =="" || phone == "" || pass_2 == "")
     {
     error.innerHTML ="موارد خالی را پر کنید";
     return false; 
     }
     if(pass.length<8){
     error.innerHTML ="حداقل طول رمز هشت کاراکتر هست";
     return false;
     }    
     if(phone.length != 11){
     error.innerHTML ="شماره موبایل را درست وارد کنید";
     return false;
     }    
     if(pass != pass_2){
     error.innerHTML ="رمز و تکرارش یکسان نیست";
     return false;
     }      
     }
     
     