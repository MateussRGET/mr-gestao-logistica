# MR Gestão Logística

Projeto acadêmico para a disciplina **Fundamentos de Programação para Internet (UNIPAR EAD)**.

## Tecnologias
PHP 8+, MySQL/MariaDB, HTML, CSS, JavaScript, Bootstrap 5, XAMPP, Git e GitHub.

## Como instalar no Windows (XAMPP)
1. Instale e abra o **XAMPP Control Panel**.
2. Clique em **Start** no **Apache** e no **MySQL**.
3. Extraia a pasta `mr-gestao-logistica` para `C:\xampp\htdocs\`.
4. Abra `http://localhost/phpmyadmin/`.
5. Clique em **Importar**, selecione `banco.sql` e confirme. O SQL cria o banco `mr_logistica` e as tabelas.
6. Abra `http://localhost/mr-gestao-logistica/` no navegador.
7. Se seu MySQL tiver senha, ajuste `config/db.php`.
8. O Bootstrap é carregado por CDN; para os estilos funcionarem, mantenha a internet ativa.

## Ordem sugerida para testar
1. Cadastre dois endereços (origem e destino).
2. Cadastre um produto e um transportador.
3. Em **Fretes**, selecione os cadastros, adicione mais produtos se quiser e clique em **Gerar frete**.
4. Em **Relatórios**, escolha a data e clique em **Baixar CSV**.
5. Teste edição e exclusão de cadastros não utilizados em fretes.

## Funcionalidades
- Dashboard inicial com contadores e fretes recentes.
- Cadastro, edição, listagem e exclusão de produtos, endereços e transportadores.
- Cadastro de fretes com relação de **um transportador para vários produtos** e quantidades.
- Alteração de status do frete e exclusão de fretes.
- Relatório por dia para download em CSV.
- Campos dinâmicos de produtos com JavaScript.
- PDO com consultas preparadas, escape de HTML e token CSRF.

## Estrutura
- `index.php`: painel inicial.
- `produtos.php`, `enderecos.php`, `transportadores.php`: cadastros.
- `fretes.php`: geração e listagem de fretes.
- `relatorio.php`: relatório por data.
- `config/`: conexão e funções.
- `assets/`: CSS e JavaScript.
- `banco.sql`: estrutura do banco.

## Versionamento
No terminal dentro da pasta do projeto:
```bash
git init
git add .
git commit -m "Cria estrutura inicial do gerenciador logístico"
```
Depois crie um repositório **público** no GitHub e siga as instruções exibidas para conectar e enviar os commits. Faça novos commits após cada alteração.

## Entrega da atividade
- Exportar **todas as telas do protótipo** do Figma para PDF.
- Enviar o link do repositório público no GitHub.
- Gravar um vídeo de 5 a 10 minutos, começando com apresentação pessoal (nome, RA, curso, polo e UNIPAR EAD), mostrar cadastro, geração de frete, telas do Figma e trechos do código.
- Publicar o vídeo no YouTube como **Não listado** e enviar o link.

**Nota:** projeto para execução local em ambiente de estudo. Para produção seria necessário configurar autenticação, permissões, backups e HTTPS.
