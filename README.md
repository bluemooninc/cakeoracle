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

###ログイン情報
```angular2html
User Id : sys, system
Password : oracle
Tnsname : XE
```
###DumpからDBを復元
```angular2html
$ docker exec -it cakeoracle_oracle_1 bash
# impdp system/oracle directory=DATA_PUMP_DIR dumpfile=MYDB.DMP log=expdp_MYDB.log
```
### クライアントから接続
```angular2html
$ sqlplus system/oracle@xe:cakeoracle_oracle_1
```


# License

MIT License.
See [LICENSE](LICENSE).

