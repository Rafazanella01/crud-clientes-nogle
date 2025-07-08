<?php 

namespace App\Models;

class Cliente{

    private ?int $id;
    private string $nome;
    private string $cpf;
    private string $telefone;
    private string $cep;
    private string $cidade;
    private string $estado;
    private string $endereco;
    private string $bairro;
    private int $numero;
    private ?string $complemento; //opcional
    private bool $ativo;

    public function __construct(
        ?int $id = null, 
        string $nome, 
        string $cpf, 
        string $telefone, 
        string $cep, 
        string $cidade, 
        string $estado, 
        string $endereco, 
        string $bairro,
        int $numero,
        ?string $complemento = null,
        bool $ativo = true,
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
        $this->cep = $cep;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->endereco = $endereco;
        $this->bairro = $bairro;
        $this->numero = $numero;
        $this->complemento = $complemento;
        $this->ativo = $ativo;    
    }

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getCpf(){
        return $this->cpf;
    }

    public function getTelefone(){
        return $this->telefone;
    }

    public function getCep(){
        return $this->cep;
    }

    public function getCidade(){
        return $this->cidade;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function getEndereco(){
        return $this->endereco;
    }

    public function getBairro(){
        return $this->bairro;
    }

    public function getNumero(){
        return $this->numero;
    }

    public function getComplemento(){
        return $this->complemento;
    }

    public function isAtivo(){
        return $this->ativo;
    }
}

?>