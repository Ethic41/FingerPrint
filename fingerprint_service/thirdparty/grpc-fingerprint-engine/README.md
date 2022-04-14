grpc-fingerprint-engine
=========================

grpc-fingerprint-engine is a Linux gRPC implementation of the DigitalPersona Fingerprint engine, to allow for usage with any programming language (php, python, js, c++ e.t.c). The assumption is that you have setup the fingerprint device on the client side and you are able to obtain FMDs (Fingerprint Minutiae Data) but need a flexible way to work with the data without the programming language limitations of the DigitalPersona SDK, if you haven't setup the device on the client side you can check [here](https://github.com/Ethic41/FingerPrint).

How to Use
===============
The project basically provides a server which is just a c++ grpc wrapper to enroll, identify and verify FMDs and also provides sample client side code that communicate with the server in any language. So the idea is you may want to write your code in a language of your choice, say maybe python, or js using the DigitalPersona fingerprint device, but the SDK is limited to C, C++ or Java, the solution is this project, you can generate the client code which receives fingerprint data (FMD) from a user (e.g from the browser), you can then use the generated code to send the FMD data to the server which can perform enrollment, verification or identification. To start using:

## 1 - Install DigitalPersona Linux SDK
[Download the SDK](https://github.com/Bexils/grpc-fingerprint-engine/releases)

You need ```root``` privilege to install the SDK

```bash

# create directory for sdk
mkdir digitalPersona && cd digitalPersona

# decompress the downloaded tar archive
tar -xvf ../digitalPersona-linux-sdk-2.2.3.tar

# install the sdk
sudo ./install

```

## 2 - Setup the FingerPrint Engine Server
To setup the fingerprint engine server you can either build from source or use our generated binaries (we assume a linux server)

## Use Generated Binaries
Simply [Download](https://github.com/Bexils/grpc-fingerprint-engine/releases) and start the server

### **OR**

## Build from Source
[Setup & Install gRPC globally](https://grpc.io/docs/languages/cpp/quickstart/#setup), then 
 
```bash

# clone this repository
git clone https://github.com/Bexils/grpc-fingerprint-engine

# move into server directory
cd grpc-fingerprint-engine/src/cpp/

# create and move into the server build directory
mkdir build && cd build

# run cmake
cmake ../

# make and build
make -j

# start the server
./fingerprint_server

```

# 

# Setup the Server Clients
This is basically the heart of the project, the ability to generate code in the language of your choosing that can communicate with the Fingerprint Engine Server. You can generate client code from the ***.proto*** file(s) [here]() for any language of your choosing if it's supported by gRPC.

## Setting up Python client code (optional)
We assume ***Python 3.7*** other versions should work, but haven't been tested
```bash

# if you don't already have pip installed
sudo apt install python3-pip

# if you don't have venv installed
sudo apt install python3-venv

# globally install python grpc and grpc-tools
sudo python3 -m pip install -r requirements.txt

# move into client code directory
cd grpc-fingerprint-engine/src/python

# create a virtual environment (named grpcenv)
python3 -m venv grpcenv

# activate the environment
chmod u+x grpcenv/bin/activate
source grpcenv/bin/activate

# install python grpc tools in the new environment
python -m pip install -r requirements.txt

# [optional step] regenerate the client code
python -m grpc_tools.protoc -I../protos --python_out=. --grpc_python_out=. ../protos/fingerprint.proto

# add the url_base64encoded fmds into the fingerprint_client.py file as required
# PS: the fingerprint_server should already be running
# then run the client (enjoy :)
python fingerprint_client.py

```

## Setting up PHP Client Code (optional)
We assume PHP 7 or later, previous versions are untested
```bash

# setup php and other prerequsites
sudo apt install php7.3 php7.3-dev php-pear phpunit

# install composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# install phpunit
wget -O phpunit https://phar.phpunit.de/phpunit-5.phar
chmod +x phpunit
sudo mv phpunit /usr/bin/phpunit

# install zlib
sudo apt install libz-dev

# install grpc
sudo pecl install grpc

# install protobuf
sudo pecl install protobuf

# in your php.ini file add the following
# under the extension section
extension=grpc
extension=protobuf

# regenerate php client code (optional)
cd grpc-fingerprint-engine/src/php
protoc --proto_path=../protos --php_out=. --grpc_out=. --plugin=protoc-gen-grpc=`which grpc_php_plugin` ../protos/fingerprint.proto

# install packages
composer install

# add the url_base64encoded fmds into the fingerprint_client.php
# file as required (check the file)
# PS: the fingerprint_server should already be running
# then run the client (enjoy :)
php fingerprint_client.php

```