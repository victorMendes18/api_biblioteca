# api_biblioteca
A API oferece um serviço completo para o gerenciamento de empréstimos de livros em bibliotecas. Permite o cadastro de dois tipos de usuários: administradores (ADM) e bibliotecários (librarian), que terão acesso às funcionalidades da API. Além disso, é possível gerenciar o acervo da biblioteca, cadastrar estudantes e controlar os empréstimos realizados por eles.

# Instalação (Windows)
## Clonando o projeto do GitHub
1. Certifique-se de ter o [Git](https://git-scm.com/downloads) instalado em sua máquina.
2. Execute o seguinte comando para clonar o repositório:

   ```bash
   git clone https://github.com/victorMendes18/api_biblioteca

## Instalar o Composer
1. Acesse a página oficial do [Composer](https://getcomposer.org/download/).
2. Faça o download da ferramenta e siga as instruções fornecidas pelo instalador para completar a instalação.
3. No terminal, navegue até o diretório do projeto clonado e execute o seguinte comando:

   ```bash
   composer install

## Executando a Aplicação

Você pode executar o projeto de duas maneiras: utilizando o Apache e MySQL do [XAMPP](https://www.apachefriends.org/pt_br/download.html) ou através do [Docker](https://www.docker.com/products/docker-desktop/), utilizando o arquivo `docker-compose.yml` localizado na raiz do projeto.

### Passos:

1. Faça uma cópia do arquivo `.env.example` e renomeie-a para `.env`.

2. No arquivo `.env`, configure as variáveis de ambiente para a conexão com o MySQL. Por padrão, o arquivo está configurado para usar o XAMPP. Para isso:
   - Inicie o XAMPP e ative o Apache e o MySQL.
   - Acesse o painel administrativo do MySQL e crie um banco de dados chamado **Biblioteca**.
   - No terminal, dentro do diretório do projeto, execute o seguinte comando para iniciar o servidor local:

    ```bash
    php artisan serve
    ```

3. Para executar o projeto com o Docker:
   - Comente as variáveis relacionadas ao XAMPP no arquivo `.env` e descomente as variáveis específicas para o Docker.
   - Inicie o Docker Desktop e, no terminal, dentro do diretório do projeto, execute o comando:

    ```bash
    docker compose up
    ```

Agora, a aplicação será configurada de acordo com o ambiente escolhido.

4. Execute os seguintes comandos no terminal para rodar as migrações, rodar os seeders e gerar a chave de autenticação JWT:

    ```bash
    php artisan migrate
    php artisan db:seed
    php artisan jwt:secret
    ```

Após esses passos, a API estará em execução. Com os seeders, será criado um usuário administrador com o e-mail **adm@gmail.com** e a senha **12345678**, permitindo que você faça login e utilize todas as rotas da API.

A documentação da API, com todas as rotas disponíveis, pode ser acessada em: [http://localhost:8000/docs](http://localhost:8000/docs). 
