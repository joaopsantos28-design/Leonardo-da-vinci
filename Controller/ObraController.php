<?php


namespace Controller;

use Model\Obra;

use Exception;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API Rest — Galeria Leonardo da Vinci"
)]

#[OA\Server(
    url: "http://leonardo-da-vinci.test",
    description: "Servidor de desenvolvimento local (Herd)"
)]

class ObraController
{
    public function __construct(private Obra $obraModel)
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
        path: "/obras",
        summary: "Lista todas as obras registradas",
        tags: ["Obras"],

        responses: [

            new OA\Response(
                response: 200,
                description: "Requisição concluída com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Obras"
                )
            ),

            new OA\Response(
                response: 404,
                description: "Erro ao listar obras"
            )
        ]
    )]

    private function index(): void
    {
        try {

            $obras = $this->obraModel->readAllObras();

            http_response_code(200);

            echo json_encode($obras);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    #[OA\Post(
        path: "/obras",
        summary: "Registro de obra",
        tags: ["Obras"],

        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: "#/components/schemas/ObraInput"
            )
        ),

        responses: [

            new OA\Response(
                response: 201,
                description: "Obra criada com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Obras"
                )
            ),

            new OA\Response(
                response: 422,
                description: "Erro ao criar obra"
            ),

            new OA\Response(
                response: 400,
                description: "Erro ao cadastrar obra"
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

        if (!empty($errors)) {

            http_response_code(422);

            echo json_encode([
                "errors" => $errors
            ]);

            return;
        }

        try {

            $idUsuario = $data["id_usuario"] ?? null;

            $id = $this->obraModel->createObra(
                $data["nome"],
                $data["ano"] ?? "",
                $data["tecnica"] ?? "",
                $data["localizacao"] ?? "",
                $data["imagem"] ?? "",
                $data["descricao"] ?? "",
                $idUsuario
            );

            $obra = $this->obraModel->readObra($id);

            http_response_code(201);

            echo json_encode($obra);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    #[OA\Get(
        path: "/obras/{id}",
        summary: "Obtendo informações de uma obra",
        tags: ["Obras"],

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
                description: "Requisição para leitura de obra realizada com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Obras"
                )
            ),

            new OA\Response(
                response: 404,
                description: "Obra não encontrada"
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

            $obra = $this->obraModel->readObra($id);

            if ($obra === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Obra não encontrada!"
                ]);

                return;
            }

            http_response_code(200);

            echo json_encode($obra);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    #[OA\Patch(
        path: "/obras/{id}",
        summary: "Atualizar obra",
        tags: ["Obras"],

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
                ref: "#/components/schemas/ObraUpdateInput"
            )
        ),

        responses: [

            new OA\Response(
                response: 200,
                description: "Obra atualizada com sucesso",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/Obras"
                )
            ),

            new OA\Response(
                response: 404,
                description: "Obra não encontrada"
            ),

            new OA\Response(
                response: 422,
                description: "Tentativa de atualização com dados inválidos"
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

            $obra = $this->obraModel->readObra($id);

            if ($obra === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Obra não encontrada!"
                ]);

                return;
            }

            $data = $this->readInput();

            $nome = $data["nome"] ?? $obra["nome"];
            $ano = $data["ano"] ?? $obra["ano"];
            $tecnica = $data["tecnica"] ?? $obra["tecnica"];
            $localizacao = $data["localizacao"] ?? $obra["localizacao"];
            $imagem = $data["imagem"] ?? $obra["imagem"];
            $descricao = $data["descricao"] ?? $obra["descricao"];

            $errors = $this->validate([
                "nome" => $nome
            ]);

            if (!empty($errors)) {

                http_response_code(422);

                echo json_encode([
                    "errors" => $errors
                ]);

                return;
            }

            $this->obraModel->updateObra(
                $id,
                $nome,
                $ano,
                $tecnica,
                $localizacao,
                $imagem,
                $descricao
            );

            $updated = $this->obraModel->readObra($id);

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
        path: "/obras/{id}",
        summary: "Exclusão de obra",
        tags: ["Obras"],

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
                description: "Obra excluída com sucesso"
            ),

            new OA\Response(
                response: 404,
                description: "Obra não encontrada"
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

            $obra = $this->obraModel->readObra($id);

            if ($obra === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Obra não encontrada!"
                ]);

                return;
            }

            $this->obraModel->deleteObra($id);

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