<?php

class Filters
{
    public function __construct()
    {
    }

    private function convert_encoding_recursive($input)
    {
        if (is_array($input)) {
            // Aplicar la función recursivamente a cada elemento del array
            return array_map(array($this, 'convert_encoding_recursive'), $input);
        } elseif (is_string($input)) {
            // Convertir la cadena a UTF-8
            return mb_convert_encoding($input, 'UTF-8', 'auto');
        }
        return $input;
    }

    public function getFilters(): String
    {
        $returnFields = array();
        try {
            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            $statement = $pdo->prepare("SELECT category FROM filters group by category");
            $statement->execute();

            $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);

            $categorysFilters = array();

            foreach ($resultado as $category) {
                $statementIn = null;
                $resultadoIn = null;
                $statementIn = $pdo->prepare("SELECT * FROM filters where `category` = :category");
                $statementIn->bindParam(':category', $category['category'], PDO::PARAM_STR);
                $statementIn->execute();
                $resultadoIn = $statementIn->fetchAll(PDO::FETCH_ASSOC);
                $categorysFilters[$category['category']] = $resultadoIn;
            }

            $returnFields["data"] = $categorysFilters;
            $returnFields["status"] = 200;
            $returnFields["message"] = "correcto";

            $returnFields = $this->convert_encoding_recursive($returnFields);
            $returnProduct = json_encode($returnFields);


            return $returnProduct;
        } catch (\Throwable $e) {
            echo var_dump($e);
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);

            return $returnProduct;
        }
    }
}
