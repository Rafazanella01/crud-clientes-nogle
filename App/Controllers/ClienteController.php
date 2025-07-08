<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\DAO\ClienteDAO;

class ClienteController{

    private ClienteDAO $dao;

    public function __construct()
    {
       $this->dao = new ClienteDAO(); 
    }

    // Funçao para criar o registro, cria o objeto e passa os dados para a DAO
    public function criar(): void
    {
        //POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo "Método não permitido";
            exit;
        }

        $nomeLimpo = preg_replace('/[^A-Za-z0-9 ]/', '',iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['nome']));
        $cpf = preg_replace('/\D/', '', $_POST['cpf']);         
        $telefone = preg_replace('/\D/', '', $_POST['telefone']); 
        $cep = preg_replace('/\D/', '', $_POST['cep']);       

        //Cria o objeto Cliente com dados do POST
        $cliente = new Cliente(
            null,
            trim($nomeLimpo),
            $cpf,
            $telefone,
            $cep,
            trim($_POST['cidade']),
            trim($_POST['estado']),
            trim($_POST['endereco']),
            trim($_POST['bairro']),
            trim($_POST['numero']),
            trim($_POST['complemento'] ?? ''),
            true
        );

        if (!$this->dao->existeCpf($cpf)){
            $this->dao->inserir($cliente);
            header("Location: /?cadastro=sucesso");
            exit;
        } else{
            $msg = urlencode('Cliente com esse CPF já está cadastrado');
            header("Location: /?cadastro=erro&msg=$msg");
            exit;
        }
        
    }

    // Lista todos os registros no DB, chamando o metodo do DAO
    public function listarTodos(): array
    {
        return $this->dao->listarTodos();
    }

    // Metodo para fazer edição dos dados
    public function editar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo "Método não permitido";
            exit;
        }

        $id = $_POST['id'] ?? null;

        if ($id){
            // Regex pra formatar os dados da edição
            $nomeLimpo = preg_replace('/[^A-Za-z0-9 ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['nome'] ?? ''));
            $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
            $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
            $cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');

            $dados = [
                'id' => $id,
                'nome' => trim($nomeLimpo),
                'cpf' => $cpf,
                'telefone' => $telefone,
                'cep' => $cep,
                'cidade' => trim($_POST['cidade'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'endereco' => trim($_POST['endereco'] ?? ''),
                'bairro' => trim($_POST['bairro'] ?? ''),
                'numero' => trim($_POST['numero'] ?? ''),
                'complemento' => trim($_POST['complemento'] ?? ''),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
            ];

            $dao = new ClienteDAO();
            $success = $dao->atualizar($dados);

            if ($success) {
                header('Location: /?edicao=sucesso');
            } else {
                $msg = urlencode('Erro ao atualizar cliente');
                header("Location: /?edicao=erro&msg=$msg");
            }
            exit;
        } else {
            $msg = urlencode('ID inválido');
            header("Location: /?edicao=erro&msg=$msg");
            exit;
        }
    }

    // Metodo para inativar pega o id por POST e passa para o metodo no DAO
    public function inativar(): void
    {
        $id = $_POST['id'] ?? null;

        if ($id){
            $dao = new ClienteDAO();
            $sucesso = $dao->inativar((int)$id);

            if ($sucesso){
                header('Location: /?inativacao=sucesso');
                exit;
            } else{
                $msg = urlencode('Erro ao inativar cliente');
                header("Location: /?inativacao=erro&msg=$msg");
                exit;
            }
        } else{
            header('Location: /?inativacao=ID inválido para inativação');
            exit;
        }
    }
}

?>