<?php

namespace Model;

use Exception;
use Model\Connection;

use OpenApi\Attributes\Property;
use PDO;
use PDOException;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Obras",
    properties: [
        new OA\Property(property: "id_obra", type: "integer"),
        new OA\Property(property: "nome", type: "string"),
        new OA\Property(property: "ano", type: "string"),
        new OA\Property(property: "tecnica", type: "string"),
        new OA\Property(property: "localizacao", type: "string"),
        new OA\Property(property: "imagem", type: "string"),
        new OA\Property(property: "descricao", type: "string"),
        new OA\Property(property: "id_usuario", type: "integer"),
        new OA\Property(property: "data_cadastro", type: "string")
    ]
)]

#[OA\Schema(
    schema: "ObraInput",
    required: [
        "nome",
        "ano",
        "tecnica",
        "localizacao",
        "imagem",
        "descricao"
    ],
    properties: [
        new OA\Property(
            property: "nome",
            type: "string",
            example: "Mona Lisa"
        ),
        new OA\Property(
            property: "ano",
            type: "string",
            example: "1503–1519"
        ),
        new OA\Property(
            property: "tecnica",
            type: "string",
            example: "Óleo sobre madeira"
        ),
        new OA\Property(
            property: "localizacao",
            type: "string",
            example: "Museu do Louvre, Paris"
        ),
        new OA\Property(
            property: "imagem",
            type: "string",
            example: "https://exemplo.com/monalisa.jpg"
        ),
        new OA\Property(
            property: "descricao",
            type: "string",
            example: "Retrato realizado por Leonardo da Vinci."
        )
    ]
)]

#[OA\Schema(
    schema: "ObraUpdateInput",
    properties: [
        new OA\Property(
            property: "nome",
            type: "string",
            example: "Mona Lisa"
        ),
        new OA\Property(
            property: "ano",
            type: "string",
            example: "1503–1519"
        ),
        new OA\Property(
            property: "tecnica",
            type: "string",
            example: "Óleo sobre madeira"
        ),
        new OA\Property(
            property: "localizacao",
            type: "string",
            example: "Museu do Louvre, Paris"
        ),
        new OA\Property(
            property: "imagem",
            type: "string",
            example: "https://exemplo.com/monalisa.jpg"
        ),
        new OA\Property(
            property: "descricao",
            type: "string",
            example: "Retrato realizado por Leonardo da Vinci."
        )
    ]
)]

class Obra
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Cadastra uma nova obra
     */
    public function createObra(
        string $nome,
        string $ano,
        string $tecnica,
        string $localizacao,
        string $imagem,
        string $descricao,
        ?int $idUsuario 
    ): int {

        try {

            $sql = "INSERT INTO obras
                    (nome, ano, tecnica, localizacao, imagem, descricao, id_usuario)
                    VALUES
                    (:nome, :ano, :tecnica, :localizacao, :imagem, :descricao, :id_usuario)";
            $stmt = $this->db->prepare($sql);

             $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindValue(":ano", $ano, PDO::PARAM_STR);
            $stmt->bindValue(":tecnica", $tecnica, PDO::PARAM_STR);
            $stmt->bindValue(":localizacao", $localizacao, PDO::PARAM_STR);
            $stmt->bindValue(":imagem", $imagem, PDO::PARAM_STR);
            $stmt->bindValue(":descricao", $descricao, PDO::PARAM_STR);

            if ($idUsuario !== null) {

                $stmt->bindValue(
                    ":id_usuario",
                    $idUsuario,
                    PDO::PARAM_INT
                );

            } else {

                $stmt->bindValue(
                    ":id_usuario",
                    null,
                    PDO::PARAM_NULL
                );
            }

            $stmt->execute();

            return (int) $this->db->lastInsertId();

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao criar obra");
        }
    }

    /**
     * Busca uma obra pelo ID
     */
    public function readObra(int $id): ?array
    {
        try {

            $sql = "SELECT
                        id_obra,
                        nome,
                        ano,
                        tecnica,
                        localizacao,
                        imagem,
                        descricao,
                        id_usuario,
                        data_cadastro
                    FROM obras
                    WHERE id_obra = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetch(
                PDO::FETCH_ASSOC
            );

            return $result ?: null;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao ler informações da obra"
            );
        }
    }

    /**
     * Lista todas as obras
     */
    public function readAllObras(): array
    {
        try {

            $sql = "SELECT
                        id_obra,
                        nome,
                        ano,
                        tecnica,
                        localizacao,
                        imagem,
                        descricao,
                        id_usuario,
                        data_cadastro
                    FROM obras
                    ORDER BY id_obra";

            $stmt = $this->db->query($sql);

            return $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao listar obras"
            );
        }
    }

    /**
     * Atualiza uma obra
     */
    public function updateObra(
        int $id,
        string $nome,
        string $ano,
        string $tecnica,
        string $localizacao,
        string $imagem,
        string $descricao
    ): bool {

        try {

            $sql = "UPDATE obras SET
                        nome = :nome,
                        ano = :ano,
                        tecnica = :tecnica,
                        localizacao = :localizacao,
                        imagem = :imagem,
                        descricao = :descricao
                    WHERE id_obra = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ":nome",
                $nome,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":ano",
                $ano,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":tecnica",
                $tecnica,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":localizacao",
                $localizacao,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":imagem",
                $imagem,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":descricao",
                $descricao,
                PDO::PARAM_STR
            );

            return $stmt->execute();

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao atualizar obra"
            );
        }
    }

    /**
     * Exclui uma obra
     */
    public function deleteObra(int $id): bool
    {
        try {

            $sql = "DELETE FROM obras
                    WHERE id_obra = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao excluir obra"
            );
        }
    }
}