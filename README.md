# Qualibrain

## Requisitos do servidor

- PHP 7.3+

Instale também as seguintes extensões do PHP:
- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) se você deseja utilizar a biblioteca HTTP\CURLRequest

Além delas, certifique-se que as seguintes extensões estão habilitadas no seu PHP:

- json (habilitado por padrão - não desabilite)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- xml (habilitado por padrão - não desabilite)

## Pré-instalação

### Virtual Host (Opcional)

Recomenda-se a utilização de virtual hosts por questões de segurança e usabilidade da aplicação, mas essa etapa é opcional. Caso queira configurar seu projeto com virtual host, siga os seguintes passos:

- Copie o arquivo de configuração padrão do seu servidor Apache e dê um nome ao seu novo arquivo:

`sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/meuprojeto.conf`

- Abra o seu novo arquivo de configuração do Qualibrain:

`sudo vim /etc/apache2/sites-available/meuprojeto.conf`

- Apague todo o código anterior do arquivo e acrescente o código seguinte (Sugestão):
      <VirtualHost *:80>
		  ServerName qualibrain.local.com
		  ServerAlias www.qualibrain.local.com
		  DocumentRoot /caminho_do_projeto/qualibrain_atualizado/public
		  ErrorLog ${APACHE_LOG_DIR}/error.log
		  CustomLog ${APACHE_LOG_DIR}/access.log combined
	  </VirtualHost>

- Agora você deverá habilitar essa configuração no seu servidor Apache, com o seguinte comando:

`sudo a2ensite /etc/apache2/sites-available/meuprojeto.conf`

- Habilitada sua configuração do seu projeto, é necessário reiniciar o servidor Apache:

`sudo systemctl restart apache2.service`

- Feito isso, por fim adicionamos nossa configuração ao virtual host:

`sudo vim /etc/hosts`

- Acrescente a seguinte linha no seu arquivo hosts:

`127.0.0.1				qualibrain.local.com`


### Habilitar o módulo de reescrita do Apache

Feito o passo anterior ou não, geralmente não deixamos exposto todo o caminho dos diretórios na URL por questões de segurança. Para que todo o caminho seja oculto, precisamos trabalhar com o .htaccess do projeto (após a instalação), mas é importante que já configuremos o módulo de reescrita do Apache para funcionar depois. Para ativar o módulo, digite o seguinte comando:

`sudo a2enmod /etc/apache2/mods-available/rewrite`

Feito isso, reinicie seu Apache:

`sudo systemctl restart apache2.service`

## Instalação

Com os passos anteriores executados e os pré-requisitos cumpridos, enfim partimos para a instalação do Qualibrain. Primeiramente, baixe o projeto na sua máquina:

`git clone https://github.com/renanvieiraqualidoc/qualibrain_atualizado.git`

## Configuração

Copie `env` para `.env` e mude o arquivo `.env` para as suas configurações de ambiente (configurações de banco de dados, base_url, etc).

Feito isso, abra o arquivo `App.php` na pasta `app/Config/` e altere a `$base_url` e o `$indexPage` para as seguintes configurações:

	public $baseURL = 'http://qualibrain.local.com/';
	public $indexPage = '';

Feitos os procedimentos, seu ambiente está pronto para rodar.
