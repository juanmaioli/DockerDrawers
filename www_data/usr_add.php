<?php 
include("head.php");
?>
<!-- Container -->
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-1"></div>
        <div class="col-md-3">
        <div class="card">
                <div class="card-header"><h3><i class="far fa-image"></i> Imagen</h3></div>
                <div class="card-body text-center">
                <img src="images/usr/avatar.png" class="img-fluid">
                </div>
            </div>
        </div> 
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h3><i class="far fa-plus-square"></i> Agregar Usuario</h3><h5> <span class="text-secondary">(Todos los campos son obligatorios)</span></h5></div>
                <div class="card-body">
                    <form id='form_book' name='form_book' action='usr_save.php' method='post' >
                        <div class="row p-2">			
                            <div class="col-md">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="usr_name" id="usr_name"  placeholder="Nombre" value="" required>
                            </div>
                            <div class="col-md">
                            <label>Apellido</label>
                            <input type="text" class="form-control" name="usr_lastname" id="usr_lastname"  placeholder="Apellido" value="" required>
                            </div>
                            <div class="col-md">
                            <div id="email" name="email"><label >Email</label></div>
                            <input type="text" class="form-control" name="usr_email" id="usr_email"  placeholder="Email" value="" onblur="validateEmail(this);" required>
                            </div>
                        </div>
                        <div class="row p-2">			
                            <div class="col-md p-1 text-center">
                            <h5 class='text-center border border-danger m-1 p-2 rounded'>La clave por defecto es el numero de 123456789</h5>
                            </div>
                        </div>
                        <div class="row p-2">			
                            <div class="col-md p-1 text-center">
                            <span class="float-left"></span>
                            <span class="float-right">
                            <button type="submit" id="btnGuardar" class='btn btn-outline-primary'><i class="far fa-save"></i>&nbsp;Guardar</button>
                            </span>
                            <input name="usr_id" type="hidden" id="usr_id" value="0">
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
            
        </div>
        <div class="col-md-1"></div>        
        </div>

</div>
<!-- /Container -->
<script>
// Solo permite ingresar numeros.
function onlyNumber(e){
	var key = window.Event ? e.which : e.keyCode
	return (key >= 48 && key <= 57)
}
//Validar Email
function validateEmail(mail) 
{
    var emailAlert = document.getElementById("email");
    var email_error  = "<label class='text-danger'><i class='far fa-times-circle'></i> Email no valido</label>";
    var email_ok     = "<label class='text-success'><i class='far fa-check-circle'></i> Email valido</label>";
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(form_book.usr_email.value))
  {
    emailAlert.innerHTML = email_ok;
    return (true)
  }
    //alert("You have entered an invalid email address!")
    emailAlert.innerHTML = email_error;
    return (false)
}
</script>
<!-- /Container -->

<?php include("footer.php"); ?>
