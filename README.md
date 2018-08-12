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
docker exec -it cakeoracle_phpfpm_1 /bin/sh

# CakePHPのcomposerをphpfpmコンテナに入ってインストール
curl -s https://getcomposer.org/installer | php

# 動作確認用のサンプルプロジェクト生成
php composer.phar create-project --prefer-dist cakephp/app bookmarker
exit
```

## サンプルプロジェクトをドキュメントルートに変更
```angular2html
$ docker-compose down
# ./data/nginx/conf/conf.d/default.conf の以下を設定
    #root        /var/www/html;
    root        /var/www/html/bookmarker/webroot;
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

- [WIP] 現在全ての動作確認が終わっていないので所によっては情報が欠落している場合があります。

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

#### SQL Developper

以下、ダウンロードし設定します。

http://www.oracle.com/technetwork/jp/developer-tools/sql-developer/downloads/index.html

#### sql plus

Oracleのサイトから以下のrpmをダウンロード

oracle-instantclient18.3-basic-18.3.0.0.0-1.x86_64.rpm 
oracle-instantclient18.3-sqlplus-18.3.0.0.0-1.x86_64.rpm 

インストール
```angular2html
$ rpm -ivh oracle-instantclient12.1-basic-18.3.0.0.0-1.x86_64.rpm
$ rpm -ivh oracle-instantclient12.1-sqlplus-18.3.0.0.0-1.x86_64.rpm
```
LD_LIBRARY_PATHを設定

$sudo vi /etc/ld.so.conf.d/oracle.conf
```angular2html
/usr/lib/oracle/12.1/client64/lib
``` 
コマンド確認
```angular2html
$ldconfig
$sqlplus64 -V
```

### CakePHP3 から接続

コンポーザーでインストール
```angular2html
cd [cakePHPをインストールしたディレクトリ]
composer require cakedc/cakephp-oracle-driver
```
In bootstrap.php load plugin with bootstrap.
```angular2html
<span class="pl-s1"><span class="pl-c1">Plugin</span><span class="pl-k">::</span>load(<span class="pl-s"><span class="pl-pds">'</span>CakeDC/OracleDriver<span class="pl-pds">'</span></span>, [<span class="pl-s"><span class="pl-pds">'</span>bootstrap<span class="pl-pds">'</span></span> <span class="pl-k">=></span> <span class="pl-c1">true</span>]);
</span>

```
データソース設定例
```angular2html
'Datasources' => [
    'default' => [
    'className' => 'CakeDC\OracleDriver\Database\OracleConnection',
    //'driver' => 'CakeDC\OracleDriver\Database\Driver\OracleOCI', # For OCI8
    'driver' => 'CakeDC\OracleDriver\Database\Driver\OraclePDO', # For PDO_OCI
    'host' => 'cakeoracle_phpfpm_1',# Database host name or IP address
    //'port' => 'nonstandard_port', # Database port number (default: 1521)
    'username' => 'system',         # Database username
    'password' => 'oracle',         # Database password
    'database' => 'XE',             # Database name (maps to Oracle's <code>SERVICE_NAME</code>)
    'sid' => 'orcl',                # Database System ID (maps to Oracle's <code>SID</code>)
    'instance' => '',               # Database instance name (maps to Oracle's <code>INSTANCE_NAME</code>)
    'pooled' => '',                 # Database pooling (maps to Oracle's <code>SERVER=POOLED</code>)
    ]
]
```


```angular2html
$ sqlplus system/oracle@xe:cakeoracle_oracle_1
```


# License

MIT License.
See [LICENSE](LICENSE).

