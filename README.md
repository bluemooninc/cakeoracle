# Docker/CakeOracle

Docker for Mac を利用して CakePHP3 + Oracle12c の環境を構築します。
Mac用ですので、Windowsは適宜読み替えてください。

# Required

* Docker for Mac

# Environment

* Mac OS X 10.13
* Docker Version 18.06.0-ce-mac70
* Docker compose 1.22
* Docker Machine 0.15

# How To Run

docker-compose を使って、イメージのビルド・コンテナ生成を行ってCakePHPのサンプルコードを動かすまでのチュートリアルです。
data/htdocsが初期のドキュメントルートになります。

## コンテナを実行してサンプル・プロジェクトを作成
```bash
# イメージ生成・コンテナ作成・実行
docker-compose up -d

＃実行中のコンテナ確認
docker ps

# phpコンテナへ入る
docker exec -it cakeoracle_php_1 /bin/sh

# 動作確認用のサンプルプロジェクト生成
composer create-project --prefer-dist cakephp/app bookmarker
exit
```

## サンプルプロジェクトをドキュメントルートに変更
```angular2html
$ docker-compose down
# docker-compose.yml の以下を設定
    volumes:
#      - "./data/htdocs:/var/www/html"
      - "./data/htdocs/bookmarker:/var/www/html"
$ docker-compose up
```
## ブラウザで確認
http://localhost

## MySQLの接続について
ドキュメントルートにphpMyAdminを置くと mysql の接続確認が楽にできます。

config.inc.php にDockerのコンテナ名を記載すれば、動作します。
```angular2html
$cfg['Servers'][$i]['host'] = 'cakeoracle_mysql_1';

```
## ORACLEコンテナについて

### ログイン情報
```angular2html
User Id : sys, system
Password : oracle
Tnsname : XE
```
### DumpからDBを復元
```angular2html
$ docker exec -it cakeoracle_oracle_1 bash
# impdp system/oracle directory=DATA_PUMP_DIR dumpfile=MYDB.DMP log=expdp_MYDB.log
```
### ホストPCから接続

ホストPCからORACLEに接続します。

### ブラウザより

#### Oracle Enterprise Management console
Chromeだと失敗したのでFireFoxで動作確認しています。
```angular2html
http://localhost:8080/em
user: sys
password: oracle
connect as sysdba: true
```
#### SQL Developper

GUIアプリで管理画面を開きます。利用については以下を参考に。

http://www.oracle.com/technetwork/jp/developer-tools/sql-developer/downloads/index.html

#### sql plus

CUIで管理します。Oracleのサイトにログインしrpmをダウンロードしてインストール。
AlpineLinuxには入っていないので別のコンテナから利用ください。

oracle-instantclient18.3-basic-18.3.0.0.0-1.x86_64.rpm 
oracle-instantclient18.3-sqlplus-18.3.0.0.0-1.x86_64.rpm 

インストール
```angular2html
$ rpm -ivh oracle-instantclient18.3-basic-18.3.0.0.0-1.x86_64.rpm
$ rpm -ivh oracle-instantclient18.3-sqlplus-18.3.0.0.0-1.x86_64.rpm
```
LD_LIBRARY_PATHを設定

$sudo vi /etc/ld.so.conf.d/oracle.conf
```angular2html
ldconfig
/usr/lib/oracle/18.3/client64/lib
``` 
設定が済んだら以下にて接続が可能となります。
```angular2html
$sqlplus system/oracle@//localhost:1521/xe
```

### CakePHP3 に ORACLE ドライバーを組み込む


OraclePDOを使用して、CakePHP３からORACLEに接続します。
詳しい説明は、以下URLを参照ください。

https://github.com/CakeDC/cakephp-oracle-driver

最初にコンポーザーでドライバーをインストールします。

```angular2html
# phpコンテナへ入る
docker exec -it cakeoracle_php_1 /bin/sh
# 通常通りにプロジェクトを生成します。
composer create-project --prefer-dist cakephp/app cakedc
# 生成した（もしくは既存の）プロジェクトに移動します。
cd cakedc
# composer で cakephp-oracle-driver をインストールします。
composer require cakedc/cakephp-oracle-driver
```

次にプラグインの設定を bootstrap.php に編集します。
```angular2html
	Plugin::load('CakeDC/OracleDriver', ['bootstrap' => true]);
```

#### app.phpにデータソースを記述

データソース設定例
```angular2html
'Datasources' => [
    'default' => [
    'className' => 'CakeDC\OracleDriver\Database\OracleConnection',
    'driver' => 'CakeDC\OracleDriver\Database\Driver\OracleOCI', # For OCI8
    //'driver' => 'CakeDC\OracleDriver\Database\Driver\OraclePDO', # For PDO_OCI
    'host' => 'cakeoracle_oracle_1', # Database host name or IP address
    //'port' => 'nonstandard_port',  # Database port number (default: 1521)
    'username' => 'system',          # Database username
    'password' => 'oracle',          # Database password
    'database' => 'xe',              # Database name (maps to Oracle's <code>SERVICE_NAME</code>)
    'sid' => 'xe',                   # Database System ID (maps to Oracle's <code>SID</code>)
    'instance' => '',                # Database instance name (maps to Oracle's <code>INSTANCE_NAME</code>)
    'pooled' => '',                  # Database pooling (maps to Oracle's <code>SERVER=POOLED</code>)
    ]
]
```

#### TODO

CakePHP3+ORACLEのサンプルコードを追加する予定です。

# License

MIT License.
See [LICENSE](LICENSE).

