<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <form action="index.php?route=cadastro" method="post">
        <input type="email" name="email_user" placeholder="nome@exemplo.com" required>
        <input type="text" name="senha_user" placeholder="Digite sua senha" required>
        <input type="text" name="senha_user" placeholder="Digite seu nome" required>
        <input type="text" name="telefone_user" placeholder="Digite seu telefone">
        <input type="text" name="cpf_user" placeholder="Digite seu CPF">
        <input type="text" name="cep_user" id="cep" placeholder="Digite seu CEP">
        <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua">
        <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade">
        <input type="text" name="numero_user" placeholder="Digite o numero da sua moradia">
        
        <button type="submit"> cadastrar </button>
    </form>
</body>

<script>
document.getElementById('cep').addEventListener('blur', function() {

    let cep = this.value.replace(/\D/g, '');

    if (cep.length === 8) {
        // Faz o GET direto para o ViaCEP, sem passar pelo PHP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(dados => {
                if (!dados.erro) {
                    // Preenche os campos da tela
                    document.getElementById("rua").value = dados.logradouro;
                    document.getElementById("cidade").value = dados.localidade;
                } else {
                    alert("CEP não encontrado!");
                }
            })
            .catch(() => alert("Erro ao buscar o CEP na API."));
    }
});
</script>
</html>
