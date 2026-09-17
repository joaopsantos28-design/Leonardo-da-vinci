<?php

namespace Controller;

use Model\User;

use Exception;
use OpenApi\Attributes as OA;

class UserController
{
    public function __construct(private User $userModel)
    {
    }

    public function ProcessRequest(string $method, ?string $id): void
    {
        header("Content-Type: application/json; charset=UTF-8");

        if ($id === null) {

            match ($method) {
                "GET" => $this->index(),
                "POST" => $this->create(),
                default => $this->methodNotAllowed(["GET", "POST"])
            };

            return;
        }

        match ($method) {
            "GET" => $this->show((int) $id),
            "PATCH" => $this->update((int) $id),
            "DELETE" => $this->delete((int) $id),
            default => $this->methodNotAllowed(["GET", "PATCH", "DELETE"])
        };
    }

    #[OA\Get(
        path: "/usuarios",
        summary: "Lista todos os usuários registrados",
        tags: ["Usuarios"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Requisição concluída com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Usuarios"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Erro ao listar usuários"
            )
        ]
    )]

    private function index(): void
    {
        try {

            $usuarios = $this->userModel->readAllUsers();

            http_response_code(200);

            echo json_encode($usuarios);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }

    #[OA\Post(
        path: "/usuarios",
        summary: "Registro de usuário",
        tags: ["Usuarios"],

        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: "#/components/schemas/UsuarioInput"
            )
        ),

        responses: [

            new OA\Response(
                response: 201,
                description: "Usuário criado com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Usuarios"
                )
            ),

            new OA\Response(
                response: 422,
                description: "Erro ao criar senha"
            ),

            new OA\Response(
                response: 409,
                description: "Usuário com e-mail existente"
            ),

            new OA\Response(
                response: 400,
                description: "Erro ao cadastrar usuário"
            ),

            new OA\Response(
                response: 500,
                description: "Erro interno do servidor"
            )
        ]
    )]

    private function create(): void
    {
        $data = $this->readInput();

        $errors = $this->validate($data);

        if (
            empty($data["senha"]) ||
            strlen($data["senha"]) < 6
        ) {

            $errors[] =
                "O campo 'senha' é obrigatório e precisa ter no mínimo 6 caracteres.";
        }

        if (!empty($errors)) {

            http_response_code(422);

            echo json_encode([
                "errors" => $errors
            ]);

            return;
        }

        try {

            if ($this->userModel->emailExists($data["email"])) {

                http_response_code(409);

                echo json_encode([
                    "errors" => [
                        "Já existe um usuário com esse e-mail."
                    ]
                ]);

                return;
            }

            $passwordHash = password_hash(
                $data["senha"],
                PASSWORD_ARGON2ID,
                [
                    'memory_cost' => 1 << 17,
                    'time_cost' => 4,
                    'threads' => 2
                ]
            );

            $id = $this->userModel->createUser(
                $data["nome"],
                $data["email"],
                $passwordHash
            );

            $usuario = $this->userModel->readUser($id);

            http_response_code(201);

            echo json_encode($usuario);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }

    #[OA\Get(
        path: "/usuarios/{id}",
        summary: "Obtendo informações de um usuário",
        tags: ["Usuarios"],

        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],

        responses: [

            new OA\Response(
                response: 200,
                description: "Requisição para leitura de usuário realizada com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Usuarios"
                )
            ),

            new OA\Response(
                response: 404,
                description: "Usuário não encontrado"
            ),

            new OA\Response(
                response: 500,
                description: "Erro interno do servidor"
            )
        ]
    )]

    private function show(int $id)
    {
        try {

            $user = $this->userModel->readUser($id);

            if ($user === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Usuário não encontrado!"
                ]);

                return;
            }

            http_response_code(200);

            echo json_encode($user);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }

    #[OA\Patch(
        path: "/usuarios/{id}",
        summary: "Atualizar usuário",
        tags: ["Usuarios"],

        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],

        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: "#/components/schemas/UsuarioUpdateInput"
            )
        ),

        responses: [

            new OA\Response(
                response: 200,
                description: "Usuário atualizado com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Usuarios"
                )
            ),

            new OA\Response(
                response: 404,
                description: "Usuário não encontrado"
            ),

            new OA\Response(
                response: 422,
                description: "Tentativa de atualização com dados inválidos"
            ),

            new OA\Response(
                response: 409,
                description: "Usuário com endereço de e-mail existente"
            ),

            new OA\Response(
                response: 500,
                description: "Erro interno do servidor"
            )
        ]
    )]

    private function update(int $id)
    {
        try {

            $user = $this->userModel->readUser($id);

            if ($user === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Usuário não encontrado!"
                ]);

                return;
            }

            $data = $this->readInput();

            $nome = $data["nome"] ?? $user["nome"];

            $email = $data["email"] ?? $user["email"];

            $errors = $this->validate([
                "nome" => $nome,
                "email" => $email
            ]);

            if (!empty($errors)) {

                http_response_code(422);

                echo json_encode([
                    "errors" => $errors
                ]);

                return;
            }

            if (
                $email !== $user["email"] &&
                $this->userModel->emailExists($email, $id)
            ) {

                http_response_code(409);

                echo json_encode([
                    "errors" => [
                        "Já existe um usuário com esse e-mail."
                    ]
                ]);

                return;
            }

            $this->userModel->updateUser(
                $id,
                $nome,
                $email
            );

            $updated = $this->userModel->readUser($id);

            http_response_code(200);

            echo json_encode($updated);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }

    #[OA\Delete(
        path: "/usuarios/{id}",
        summary: "Exclusão de usuário",
        tags: ["Usuarios"],

        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],

        responses: [

            new OA\Response(
                response: 204,
                description: "Usuário excluído com sucesso"
            ),

            new OA\Response(
                response: 404,
                description: "Usuário não encontrado"
            ),

            new OA\Response(
                response: 500,
                description: "Erro interno do servidor"
            )
        ]
    )]

    private function delete(int $id)
    {
        try {

            $user = $this->userModel->readUser($id);

            if ($user === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Usuário não encontrado!"
                ]);

                return;
            }

            $this->userModel->deleteUser($id);

            http_response_code(204);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }

    private function readInput(): array
    {
        $body = file_get_contents("php://input");

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data["nome"])) {

            $errors[] =
                "O campo 'nome' é obrigatório.";
        }

        if (empty($data["email"])) {

            $errors[] =
                "O campo 'email' é obrigatório.";

        } elseif (!filter_var(
            $data["email"],
            FILTER_VALIDATE_EMAIL
        )) {

            $errors[] =
                "O campo 'email' precisa ser um e-mail válido.";
        }

        return $errors;
    }

    private function methodNotAllowed(array $allowed): void
    {
        header(
            "Allow: " . implode(", ", $allowed)
        );

        http_response_code(405);

        echo json_encode([
            "error" => "Método não permitido"
        ]);
    }
}