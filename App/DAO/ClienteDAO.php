<?php
namespace App\DAO;

use App\Database\Database;
use App\Models\Cliente;
use PDO;

class ClienteDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    // Metodo para inserir no banco, passando o objeto do cliente
    public function inserir(Cliente $cliente): bool
    {
        $sql = "INSERT INTO clientes 
                (nome, cpf, telefone, cep, cidade, estado, endereco, bairro, numero, complemento, ativo) 
                VALUES 
                (:nome, :cpf, :telefone, :cep, :cidade, :estado, :endereco, :bairro, :numero, :complemento, :ativo)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nome' => $cliente->getNome(),
            ':cpf' => $cliente->getCpf(),
            ':telefone' => $cliente->getTelefone(),
            ':cep' => $cliente->getCep(),
            ':cidade' => $cliente->getCidade(),
            ':estado' => $cliente->getEstado(),
            ':endereco' => $cliente->getEndereco(),
            ':bairro' => $cliente->getBairro(),
            ':numero' => $cliente->getNumero(),
            ':complemento' => $cliente->getComplemento(),
            ':ativo' => 1,
        ]);
    }

    // Listar todos registros do DB
    public function listarTodos(): array
    {
        $sql = "SELECT * FROM clientes ORDER BY nome";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar com filtro
    public function listarComFiltro(?string $nome, ?string $cpf): array
    {
        $sql = "SELECT * FROM clientes WHERE 1 = 1";
        $params = [];

        if ($nome) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = '%' . $nome . '%';
        }

        if ($cpf) {
            $cpfLimpo = preg_replace('/\D/', '', $cpf);
            $sql .= " AND cpf LIKE :cpf";
            $params[':cpf'] = '%' . $cpfLimpo . '%';
        }

        $sql .= " ORDER BY nome";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metodo para atualizar os registros
    public function atualizar(array $cliente): bool
    {
        $sql = "UPDATE clientes SET 
                    nome = :nome,
                    cpf = :cpf,
                    telefone = :telefone,
                    cep = :cep,
                    cidade = :cidade,
                    estado = :estado,
                    endereco = :endereco,
                    bairro = :bairro,
                    numero = :numero,
                    complemento = :complemento,
                    ativo = :ativo
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $cliente['id'],
            ':nome' => $cliente['nome'],
            ':cpf' => $cliente['cpf'],
            ':telefone' => $cliente['telefone'],
            ':cep' => $cliente['cep'],
            ':cidade' => $cliente['cidade'],
            ':estado' => $cliente['estado'],
            ':endereco' => $cliente['endereco'],
            ':bairro' => $cliente['bairro'],
            ':numero' => $cliente['numero'],
            ':complemento' => $cliente['complemento'],
            ':ativo' => $cliente['ativo'],
        ]);
    }

    //Verifica se já existe o cpf passado no parametro no DB
    public function existeCpf(string $cpf, ?int $ignorarId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM clientes WHERE cpf = :cpf";

        if ($ignorarId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':cpf', $cpf);
        
        if ($ignorarId !== null) {
            $stmt->bindValue(':id', $ignorarId, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Inativa por Id
    public function inativar(int $id): bool
    {
        $sql = "UPDATE clientes SET ativo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
