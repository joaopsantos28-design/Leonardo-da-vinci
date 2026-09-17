<?php

namespace Model;

use Exception;
use Model\Connection;

use OpenApi\Attributes\Property;
use PDO;
use PDOException;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Usuarios",
    properties: [
        new OA\Property(property: "id_usuario", type: "integer"),
        new OA\Property(property: "nome", type: "string"),
        new OA\Property(property: "email", type: "string"),
        new OA\Property(property: "senha", type: "string", format: "password"),
        new OA\Property(property: "data_cadastro", type: "string", format: "date-time")
    ]
)]

#[OA\Schema(
    schema: "UsuarioInput",
    required: ["nome", "email", "senha"],
    properties: [
        new OA\Property(
            property: "nome",
            type: "string",
            example: "Leonardo da Silva"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            example: "leonardo@email.com"
        ),
        new OA\Property(
            property: "senha",
            type: "string",
            minLength: 6,
            example: "senha123"
        )
    ]
)]

#[OA\Schema(
    schema: "UsuarioUpdateInput",
    properties: [
        new OA\Property(
            property: "nome",
            type: "string",
            example: "Leonardo da Silva"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            example: "leonardo@email.com"
        )
    ]
)]

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function createUser(
        string $nome,
        string $email,
        string $senha
    ): int {
        try {

            $sql = 'INSERT INTO usuarios
                    (nome, email, senha, data_cadastro)
                    VALUES
                    (:nome, :email, :senha, NOW())';

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(
                ":nome",
                $nome,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":email",
                $email,
                PDO::PARAM_STR
            );

            $stmt->bindParam(
                ":senha",
                $senha,
                PDO::PARAM_STR
            );

            $stmt->execute();

            return (int) $this->db->lastInsertId();

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao criar usuário"
            );
        }
    }

    public function readUser(int $id): ?array
    {
        try {

            // Primeira etapa de segurança -
            // NUNCA mostrar a senha
            $sql = "SELECT
                        id_usuario,
                        nome,
                        email,
                        data_cadastro
                    FROM usuarios
                    WHERE id_usuario = :id";

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
                "Erro ao ler informações do usuário"
            );
        }
    }

    public function readAllUsers(): array
    {
        try {

            $sql = "SELECT
                        id_usuario,
                        nome,
                        email,
                        data_cadastro
                    FROM usuarios
                    ORDER BY id_usuario";

            $stmt = $this->db->query($sql);

            return $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao listar usuários"
            );
        }
    }

    public function updateUser(
        int $id,
        string $nome,
        string $email
    ): bool {
        try {

            $sql = "UPDATE usuarios
                    SET nome = :nome,
                        email = :email
                    WHERE id_usuario = :id";

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
                ":email",
                $email,
                PDO::PARAM_STR
            );

            return $stmt->execute();

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao atualizar usuário"
            );
        }
    }

    public function deleteUser(int $id): bool
    {
        try {

            $sql = "DELETE FROM usuarios
                    WHERE id_usuario = :id";

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
                "Erro ao excluir usuário"
            );
        }
    }

    public function emailExists(
        string $email,
        ?int $excludeId = null
    ): bool {
        try {

            $sql = "SELECT id_usuario
                    FROM usuarios
                    WHERE email = :email";

            if ($excludeId !== null) {

                $sql .= " AND id_usuario != :excludeId";
            }

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(
                ":email",
                $email,
                PDO::PARAM_STR
            );

            if ($excludeId !== null) {

                $stmt->bindValue(
                    ":excludeId",
                    $excludeId,
                    PDO::PARAM_INT
                );
            }

            $stmt->execute();

            return $stmt->fetch() !== false;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception(
                "Erro ao verificar e-mail"
            );
        }
    }
}