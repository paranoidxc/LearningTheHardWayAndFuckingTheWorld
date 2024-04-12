#!/usr/bin/env bash

# 使用方法：
# ./genModel.sh dbname tablename cache
# ./genModel.sh dbname tablename
# 再将./genModel下的文件剪切到对应服务的model目录里面，记得改package

#生成的表名
tables=$2
#表生成的genmodel目录
modeldir=../api/model/

cache=$3

# 数据库配置
host=127.0.0.1
port=13306
dbname=$1
username=root
passwd=fucking


if [ -z "$cache" ]; then
  # cache 为空
  echo "开始创建库：$dbname 的表：$2"
  goctl model mysql datasource -url="${username}:${passwd}@tcp(${host}:${port})/${dbname}" -table="${tables}"  -dir="${modeldir}" --style=goZero
else
 echo "开始创建库：$dbname 的表带cache：$2"
 goctl model mysql datasource -url="${username}:${passwd}@tcp(${host}:${port})/${dbname}" -table="${tables}"  -dir="${modeldir}" -cache=true --style=goZero
fi


#if [$cache -eq "cache"]; then
#  goctl model mysql datasource -url="${username}:${passwd}@tcp(${host}:${port})/${dbname}" -table="${tables}"  -dir="${modeldir}" -cache=true --style=goZero
#else
#  goctl model mysql datasource -url="${username}:${passwd}@tcp(${host}:${port})/${dbname}" -table="${tables}"  -dir="${modeldir}" --style=goZero
#fi
