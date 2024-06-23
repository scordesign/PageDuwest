<?php
class Amounts
{


    public function __construct()
    {
    }

    public function getAmounts(): string
    {
        $resultado = array();
        try {
            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            $statement = $pdo->prepare("SELECT * FROM amount");
            $statement->execute();

            $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);

            $returnFields["data"] = $resultado;
            $returnFields["status"] = 200;
            $returnFields["message"] = "correcto";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        } catch (\Throwable $e) {
            
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();
            $returnProduct = json_encode($returnFields);
            
            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }

}
?>