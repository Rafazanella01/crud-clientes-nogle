<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <title>CRUD Clientes - Nogle</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .custom-alert {
      position: fixed;
      top: 20px;
      right: 20px;
      max-width: 500px;
      min-width: 380px;
      padding: 0.3rem 0.8rem;
      font-size: 0.85rem;
      z-index: 1050;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      margin: 0;
    }

    .status-icon {
      font-size: 1.2rem;
    }

    .status-ativo {
      color: green;
    }

    .status-inativo {
      color: red;
    }
  </style>
</head>

<body class="p-4">

  <div class="container">
    <h2 class="mb-3">Cadastro Clientes Nogle</h2>

    <!-- filtro clientes -->
    <form method="get" class="mb-3 d-flex gap-2">
      <input name="nome" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" placeholder="Filtrar por nome" class="form-control" />
      <input id="filtroCPF" name="cpf" value="<?= htmlspecialchars($_GET['cpf'] ?? '') ?>" placeholder="Filtrar por CPF" class="form-control" />
      <button type="submit" class="btn btn-primary">Buscar</button>
      <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#clienteModal">Novo Cliente</button>
    </form>

    <!-- logica da tabela -->
    <div id="tabelaClientes" style="overflow-x: auto;">
      <table class="table table-bordered align-middle text-nowrap">
        <thead class="table-light">
          <tr>
            <th>Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>Endereço</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php

          use App\DAO\ClienteDAO;

          $nome = trim($_GET['nome'] ?? '');
          $cpf = trim($_GET['cpf'] ?? '');

          $dao = new ClienteDAO();
          if ($nome || $cpf) {
            $clientes = $dao->listarComFiltro($nome, $cpf);
          } else {
            $clientes = $dao->listarTodos();
          }

          // Formata CPF: 12345678901 -> 123.456.789-01
          function formatarCpf(string $cpf): string
          {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
          }

          // Formata CEP: 12345678 -> 12345-678
          function formatarCep(string $cep): string
          {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
          }

          // Formata telefone com DDD
          function formatarTelefone(string $telefone): string
          {
            $numero = preg_replace('/\D/', '', $telefone);
            if (strlen($numero) === 11) {
              return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $numero);
            } elseif (strlen($numero) === 10) {
              return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $numero);
            } else {
              return $telefone;
            }
          }

          if (empty($clientes)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">
                <?php if (!empty($_GET['nome']) || !empty($_GET['cpf'])): ?>
                  Cliente não encontrado.
                <?php else: ?>
                  Nenhum cliente cadastrado. Clique em "Novo Cliente" para adicionar.
                <?php endif; ?>
              </td>
            </tr>
            <?php else:
            foreach ($clientes as $cliente):
              $nome = htmlspecialchars($cliente['nome'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $cpf = formatarCpf(preg_replace('/\D/', '', $cliente['cpf'] ?? ''));
              $telefone = formatarTelefone($cliente['telefone'] ?? '');
              $cep = formatarCep(preg_replace('/\D/', '', $cliente['cep'] ?? ''));
              $cidade = htmlspecialchars($cliente['cidade'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $estado = htmlspecialchars($cliente['estado'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $endereco = htmlspecialchars($cliente['endereco'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $bairro = htmlspecialchars($cliente['bairro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $numero = htmlspecialchars($cliente['numero'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $complemento = htmlspecialchars($cliente['complemento'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
              $ativo = (bool) $cliente['ativo'];

              $enderecoCompleto = "$endereco, $numero - $bairro - $cidade/$estado - CEP: $cep";
              $statusClass = $ativo ? 'text-success' : 'text-danger';
              $statusIcon = $ativo ? 'Ativo' : 'Inativo';
            ?>
              <tr>
                <td><?= $nome ?></td>
                <td><?= $cpf ?></td>
                <td><?= $telefone ?></td>
                <td><?= $enderecoCompleto ?></td>
                <td class="text-center <?= $statusClass ?>" title="<?= $ativo ? 'Ativo' : 'Inativo' ?>">
                  <?= $statusIcon ?>
                </td>
                <td>
                  <button
                    class="btn btn-sm btn-primary btn-editar"
                    data-id="<?= (int)$cliente['id'] ?>"
                    data-nome="<?= $nome ?>"
                    data-cpf="<?= $cpf ?>"
                    data-telefone="<?= $telefone ?>"
                    data-cep="<?= $cep ?>"
                    data-cidade="<?= $cidade ?>"
                    data-estado="<?= $estado ?>"
                    data-endereco="<?= $endereco ?>"
                    data-bairro="<?= $bairro ?>"
                    data-numero="<?= $numero ?>"
                    data-complemento="<?= $complemento ?>"
                    data-ativo="<?= $ativo ? '1' : '0' ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#editarModal">Editar</button>

                  <?php if ($ativo): ?>
                    <form method="post" action="/inativar" style="display:inline-block;" onsubmit="return confirm('Deseja realmente inativar este cliente?');">
                      <input type="hidden" name="id" value="<?= (int)$cliente['id'] ?>" />
                      <button type="submit" class="btn btn-sm btn-danger">Inativar</button>
                    </form>
                  <?php else: ?>
                    <button class="btn btn-sm btn-secondary" disabled>Inativado</button>
                  <?php endif; ?>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal Cadastrar -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formCliente" method="post" action="/criar" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cadastrar Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <input name="nome" id="nome" class="form-control mb-2" placeholder="Nome Cliente" required />
            <input name="cpf" id="cpf" class="form-control mb-2" placeholder="CPF" required />
            <input name="telefone" id="telefone" class="form-control mb-2" placeholder="Telefone" required />
            <input name="cep" id="cep" class="form-control mb-2" placeholder="CEP" required />
            <input name="cidade" id="cidade" class="form-control mb-2" placeholder="Cidade" required />
            <input name="estado" id="estado" class="form-control mb-2" placeholder="Estado" required />
            <input name="endereco" id="endereco" class="form-control mb-2" placeholder="Endereço" required />
            <input name="bairro" id="bairro" class="form-control mb-2" placeholder="Bairro" required />
            <input name="numero" id="numero" class="form-control mb-2" placeholder="Número" required />
            <input name="complemento" id="complemento" class="form-control mb-2" placeholder="Complemento (opcional)" />
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formEditarCliente" method="post" action="/editar" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="editarId" />
            <input name="nome" id="editarNome" class="form-control mb-2" placeholder="Nome Cliente" required />
            <input name="cpf" id="editarCpf" class="form-control mb-2" placeholder="CPF" required />
            <input name="telefone" id="editarTelefone" class="form-control mb-2" placeholder="Telefone" required />
            <input name="cep" id="editarCep" class="form-control mb-2" placeholder="CEP" required />
            <input name="cidade" id="editarCidade" class="form-control mb-2" placeholder="Cidade" required />
            <input name="estado" id="editarEstado" class="form-control mb-2" placeholder="Estado" required />
            <input name="endereco" id="editarEndereco" class="form-control mb-2" placeholder="Endereço" required />
            <input name="bairro" id="editarBairro" class="form-control mb-2" placeholder="Bairro" required />
            <input name="numero" id="editarNumero" class="form-control mb-2" placeholder="Número" required />
            <input name="complemento" id="editarComplemento" class="form-control mb-2" placeholder="Complemento (opcional)" />
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="editarAtivo" name="ativo" value="1" />
              <label class="form-check-label" for="editarAtivo">Ativo</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script>
      // mascaras para pegar apenas dados esperados
      IMask(document.getElementById('filtroCPF'), {
        mask: '000.000.000-00'
      });
      IMask(document.getElementById('cpf'), {
        mask: '000.000.000-00'
      });
      IMask(document.getElementById('telefone'), {
        mask: '(00) 00000-0000'
      });
      IMask(document.getElementById('cep'), {
        mask: '00000-000'
      });

      // mascaras do modal de edição

      IMask(document.getElementById('editarCpf'), {
        mask: '000.000.000-00'
      });
      IMask(document.getElementById('editarTelefone'), {
        mask: '(00) 00000-0000'
      });
      IMask(document.getElementById('editarCep'), {
        mask: '00000-000'
      });

      // Preencher modal de edição com dados do botão Editar
      document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', () => {
          document.getElementById('editarId').value = button.getAttribute('data-id');
          document.getElementById('editarNome').value = button.getAttribute('data-nome');
          document.getElementById('editarCpf').value = button.getAttribute('data-cpf');
          document.getElementById('editarTelefone').value = button.getAttribute('data-telefone');
          document.getElementById('editarCep').value = button.getAttribute('data-cep');
          document.getElementById('editarCidade').value = button.getAttribute('data-cidade');
          document.getElementById('editarEstado').value = button.getAttribute('data-estado');
          document.getElementById('editarEndereco').value = button.getAttribute('data-endereco');
          document.getElementById('editarBairro').value = button.getAttribute('data-bairro');
          document.getElementById('editarNumero').value = button.getAttribute('data-numero');
          document.getElementById('editarComplemento').value = button.getAttribute('data-complemento');
          document.getElementById('editarAtivo').checked = button.getAttribute('data-ativo') == '1';
        });
      });

      //Alerta cadastro:
      if (
        window.location.search.includes('cadastro=') ||
        window.location.search.includes('edicao=') ||
        window.location.search.includes('inativacao=') ||
        window.location.search.includes('error=')
      ) {
        const url = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, url);
      }

      setTimeout(() => {
        ['alertCadastro', 'alertEdicao', 'alertInativacao'].forEach(id => {
          const alert = document.getElementById(id);
          if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
          }
        });
      }, 4000);

      // Ocultar alerta de erro após 6 segundos
      setTimeout(() => {
        const alertError = document.getElementById('alertError');
        if (alertError) {
          alertError.classList.remove('show');
          alertErrors.classList.add('fade');
          const bsAlert = bootstrap.Alert.getOrCreateInstance(alertError);
          bsAlert.close();
        }
      }, 6000);
    </script>

    <?php
    $cadastroSucesso = isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso';
    $cadastroErro = isset($_GET['cadastro']) && $_GET['cadastro'] === 'erro';
    $edicaoSucesso = isset($_GET['edicao']) && $_GET['edicao'] === 'sucesso';
    $edicaoErro = isset($_GET['edicao']) && $_GET['edicao'] === 'erro';
    $inativacaoSucesso = isset($_GET['inativacao']) && $_GET['inativacao'] === 'sucesso';
    $inativacaoErro = isset($_GET['inativacao']) && $_GET['inativacao'] === 'erro';
    $mensagemErro = $_GET['msg'] ?? null;
    ?>

    <?php if ($cadastroSucesso): ?>
      <div id="alertCadastro" class="alert alert-success custom-alert" role="alert">
        Cliente cadastrado com sucesso!
      </div>
    <?php endif; ?>

    <?php if ($cadastroErro && $mensagemErro): ?>
      <div id="alertError" class="alert alert-danger custom-alert" role="alert">
        Erro ao cadastrar: <?= htmlspecialchars(urldecode($mensagemErro)) ?>
      </div>
    <?php endif; ?>

    <?php if ($edicaoSucesso): ?>
      <div id="alertEdicao" class="alert alert-success custom-alert" role="alert">
        Cliente editado com sucesso!
      </div>
    <?php endif; ?>

    <?php if ($edicaoErro && $mensagemErro): ?>
      <div id="alertError" class="alert alert-danger custom-alert" role="alert">
        Erro ao editar: <?= htmlspecialchars(urldecode($mensagemErro)) ?>
      </div>
    <?php endif; ?>

    <?php if ($inativacaoSucesso): ?>
      <div id="alertInativacao" class="alert alert-warning custom-alert" role="alert">
        Cliente inativado com sucesso!
      </div>
    <?php endif; ?>

    <?php if ($inativacaoErro && $mensagemErro): ?>
      <div id="alertError" class="alert alert-danger custom-alert" role="alert">
        Erro ao inativar: <?= htmlspecialchars(urldecode($mensagemErro)) ?>
      </div>
    <?php endif; ?>

</body>

</html>