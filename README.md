# TESTE para a vaga PHP Dev - Dextra
Neste respositório estão todos os arquivos necessários para rodar o projeto sem necessidade de conexão com a Internet.

##Instalação
Esta instalação considera que a máquina onde o projeto vai rodar possui instalado um servidor Apache, PHP e Mysql.

Basta seguir a sequência de atividades:

1 - Descompacte os arquivos na pasta destino, se ainda não o fez.

2 - Crie um novo Banco de Dados e tenha em mãos os dados de acesso.

3 - Abra o arquivo 'config.php' que se encontra na raiz do projeto.

4 - Edite os campos de acesso ao MySQL (HOST, DB, USER e PASS);

5 - No banco de dados, rode o script 'db_50_chars.sql' que se encontra na raiz do projeto.

6 - Os dados inseridos neste script já apresentam 50 personagens e as suas comics, events, series e stories

7 - A pasta 'images' contém todas as imagens desdes personagens e seus outros endpoints, para poder rodar offline

9 - Se houver necessidade ou interesse de resetar o banco para fazer carregar todo conteúdo,
minhas chaves e os scripts para rodar estão no arquivo '_connect_marvel_api.php'

10 - Dentro desde arquivo é possível alterar a quantidade de personagens na declaração da string '$limit';

11 - Para rodar este arquivo basta acessar a página 'index.php' e procurar o código PHP que carrega este arquivo, 
executando uma busca ou procurando aproximadamente pela linha 152

12 - Nos meus testes as vezes precisava dar F5 mais de uma vez para rodar, como se o primeiro acesso fosse somente de autenticação.

13 - Ao testar a interface, o personagem A.I.M. pareceu o mais  interessante pois tem uma boa quantidade de comics para testar o scroll vertical

14 - Somente os comics foram puxados pelo ajax pois os demais endpoints são idênticos na apresentação, variando apenas um ou outro nome de string.

15 - Acredito que vai ser fácil perceber que estes códigos foram escritos do zero, linha por linha.
Não houve busca de códigos na internet nem aproveitamento de qualquer código já escrito.
Desta forma os scripts, o PHP e o CSS estão totalmente minimalistas.

16 - E boa sorte pra mim!

