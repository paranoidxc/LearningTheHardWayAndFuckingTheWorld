#!/usr/bin/env bash

# 使用方法：
# ./genApi.sh

goctl api go -api ../api/memo/main.api -dir ../api/cmder/ --style=goZero --home=../tpl/
