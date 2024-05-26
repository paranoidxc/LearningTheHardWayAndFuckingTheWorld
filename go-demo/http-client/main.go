package main

import (
	"abc/thirdPartServer"
	"fmt"
	"net/http"
	"time"
)

func main() {
	//x := thirdPartServer.HeaderWithForm( thirdPartServer.HeaderWithJson(thirdPartServer.Header()))
	//fmt.Println(x)
	doGet()
	time.Sleep(time.Second)
	fmt.Println("===========")

	doGet2()
	time.Sleep(time.Second)
	fmt.Println("===========")

	doGet3()
	time.Sleep(time.Second)
	fmt.Println("===========")

	doGet4()
	time.Sleep(time.Second)
	fmt.Println("===========")

	doPost()
	time.Sleep(time.Second)
	fmt.Println("===========")
	doPostJson()
}

func doGet() {
	fmt.Println("doGet string")
	path := "http://www.baidu.com"
	header := map[string]string{}
	params := "id=1&b=2"
	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodGet, header, params)
	if err != nil {
		fmt.Println("err", err)

	}
	fmt.Println("resp:", resp)
}

func doGet2() {
	fmt.Println("doGet2 map")
	path := "http://www.baidu.com"
	header := map[string]string{}
	params := map[string]string{
		"id": "1",
		"b":  "2",
	}

	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodGet, header, params)
	if err != nil {
		fmt.Println("err", err)

	}
	fmt.Println("resp:", resp)
}

func doGet3() {
	fmt.Println("doGet3 map string interface")
	path := "http://www.baidu.com"
	params := map[string]interface{}{
		"id": 1,
		"b":  "hello2",
	}
	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodGet, nil, params)
	if err != nil {
		fmt.Println("err", err)

	}
	fmt.Println("resp:", resp)
}

func doGet4() {
	fmt.Println("doGet4 slice string interface")
	path := "http://www.baidu.com"
	params := []string{
		"id=1",
		"b=hello",
	}
	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodGet, nil, params)
	if err != nil {
		fmt.Println("err", err)
	}
	fmt.Println("resp:", resp)
}

func doPost() {
	fmt.Println("doPost map string interface")
	path := "http://www.baidu.com"
	header := thirdPartServer.Header()
	params := map[string]interface{}{
		"id": 1,
		"b":  "hello2",
	}
	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodPost, header, params)
	if err != nil {
		fmt.Println("err", err)
	}
	fmt.Println(resp)
}

func doPostJson() {
	fmt.Println("doPost json string interface")
	path := "http://www.baidu.com"

	header := thirdPartServer.HeaderWithJson(nil)
	params := struct {
		Name string `json:"name"`
		Age  int    `json:"age"`
	}{
		Name: "hello",
		Age:  123,
	}
	resp, err := thirdPartServer.DoHttpRequest(path, http.MethodPost, header, params)
	if err != nil {
		fmt.Println("err", err)
	}
	fmt.Println("resp:", resp)
}
