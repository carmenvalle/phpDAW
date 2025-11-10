<?php
    $usuarios = crearUsuarios(); 
    $aceptado = true;
    $error = "";

function crearUsuarios() {
    
    return $datos = [
        ["usuario1","usuario1","default"],
        ["usuario2","usuario2","modo_oscuro"],
        ["silvia","silvia123","letra_grande"],
        ["carmen","carmen123","alto_contraste"]
    ];

}

function comprobarUsuario(){
    global $usuarios;
    if(isset($_POST["nomUsuario"]) && isset($_POST["pass"])){
        $userName = $_POST["nomUsuario"];
        $pass = $_POST["pass"];

        $encontrado = false;
        foreach($usuarios as $user){
            if($user[0]===$userName && $user[1]===$pass){
                $encontrado = true;
            }
        }
        if(!$encontrado){
            echo "
                <p style='color:red'><strong>Usuario no encontrado, introduzca un usuario válido</strong></p>
            ";
        }
        else{
            header("Location: index_logueado.php");
            exit;
        }
    }
    else{
        echo " <p style='color:red'><strong>Por favor introduzca una Nombre de Usuario y una Contraseña</strong></p>";
    }
}
?>