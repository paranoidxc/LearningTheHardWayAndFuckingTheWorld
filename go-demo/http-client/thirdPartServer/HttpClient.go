package thirdPartServer

import (
	"bytes"
	"encoding/json"
	"errors"
	"fmt"
	"net/http"
	"net/url"
	"reflect"
	"strings"
)

func HeaderWithJson(header map[string]string) map[string]string {
	if header == nil {
		header = map[string]string{}
	}
	header["Content-Type"] = "application/json"
	return header
}

func HeaderWithForm(header map[string]string) map[string]string {
	if header == nil {
		header = map[string]string{}
	}
	header["Content-Type"] = "application/x-www-form-urlencoded"
	return header
}

func Header() map[string]string {
	return map[string]string{}
}

func sliceToFormBody(reqSlice []string, reqFormBody url.Values) url.Values {
	if reqFormBody == nil {
		reqFormBody = url.Values{}
	}
	for _, v := range reqSlice {
		tmp := strings.Split(v, "=")
		reqFormBody.Set(tmp[0], tmp[1])
	}

	return reqFormBody
}

func parseRequestParams(path string, method string, header map[string]string, req interface{}) (buf *bytes.Buffer, err error) {
	defer func() {
		if xerr := recover(); xerr != nil {
			err = errors.New(fmt.Sprintf("捕获到[panic]异常:%+v", xerr))
		}
	}()
	reqFormBody := url.Values{}
	var reqJsonBody []byte
	if req != nil {
		kind := reflect.TypeOf(req).Kind()
		switch kind {
		case reflect.Map:
			fmt.Printf("type:%+v\n", kind)
			reqSlice := []string{}
			reqVal := reflect.ValueOf(req)
			keys := reqVal.MapKeys()
			for _, key := range keys {
				value := reqVal.MapIndex(key)
				reqSlice = append(reqSlice, fmt.Sprintf("%v=%v", key.Interface(), value.Interface()))
			}
			if method == http.MethodGet {
				path += "?" + strings.Join(reqSlice, "&")
			}
			if method == http.MethodPost {
				reqFormBody = sliceToFormBody(reqSlice, reqFormBody)
			}
			fmt.Printf("params:%s\n", strings.Join(reqSlice, "&"))
		case reflect.String:
			fmt.Printf("type string:%s\n", req.(string))
			if method == http.MethodGet {
				path += "?" + req.(string)
			}
		case reflect.Struct:
			fmt.Printf("type:%+v\n", kind)
			var xerr error
			reqJsonBody, xerr = json.Marshal(req)
			if xerr != nil {
				return nil, fmt.Errorf("json.Marshal求数据失败: %v", err)
			}
		case reflect.Slice:
			fmt.Printf("type:%+v\n", kind)
			fmt.Println("reqslice:", req)
			if reqSlice, ok := req.([]string); ok {
				if method == http.MethodGet {
					path += "?" + strings.Join(reqSlice, "&")
				} else {
					reqFormBody = sliceToFormBody(reqSlice, reqFormBody)
				}
			} else {
				return nil, fmt.Errorf("非预期切片类型无法处理")
			}
		default:
			return buf, fmt.Errorf("不支持请求参数类型: %v", err)
		}
	}

	isJson := false
	if header != nil {
		for key, value := range header {
			if strings.ContainsAny(value, "json") {
				isJson = true
			}
			fmt.Println("key", key, "val", value)
		}
	}

	if isJson {
		buf = bytes.NewBuffer(reqJsonBody)
	} else {
		buf = bytes.NewBufferString(reqFormBody.Encode())
	}

	fmt.Println(
		"请求第三方信息",
		"path", path, "\t",
		"method", method, "\t",
		"reqJson", string(reqJsonBody), "\t",
		"reqForm", reqFormBody, "\t",
		"isJson", isJson,
	)

	return
}

func DoHttpRequest(path string, method string, header map[string]string, req interface{}) (body []byte, err error) {
	defer func() {
		if xerr := recover(); xerr != nil {
			err = errors.New(fmt.Sprintf("捕获到[panic]异常:%+v", xerr))
		}
	}()

	buffer, err := parseRequestParams(path, method, header, req)
	if err != nil {
		return
	}

	var request *http.Request
	request, err = http.NewRequest(method, path, buffer)

	if header != nil {
		for key, value := range header {
			request.Header.Set(key, value)
		}
	}

	return
}

func DoHttpRequest22(path string, method string, header map[string]string, req interface{}) (body []byte, err error) {
	defer func() {
		if xerr := recover(); xerr != nil {
			err = errors.New(fmt.Sprintf("捕获到[panic]异常:%+v", xerr))
		}
	}()

	reqFormBody := url.Values{}
	var reqJsonBody []byte
	if req != nil {
		kind := reflect.TypeOf(req).Kind()
		switch kind {
		case reflect.Map:
			fmt.Printf("type:%+v\n", kind)
			reqSlice := []string{}
			reqVal := reflect.ValueOf(req)
			keys := reqVal.MapKeys()
			for _, key := range keys {
				value := reqVal.MapIndex(key)
				reqSlice = append(reqSlice, fmt.Sprintf("%v=%v", key.Interface(), value.Interface()))
			}
			if method == http.MethodGet {
				path += "?" + strings.Join(reqSlice, "&")
			}
			if method == http.MethodPost {
				for _, v := range reqSlice {
					tmp := strings.Split(v, "=")
					reqFormBody.Set(tmp[0], tmp[1])
				}
			}
			fmt.Printf("params:%s\n", strings.Join(reqSlice, "&"))
		case reflect.String:
			fmt.Printf("type string:%s\n", req.(string))
			if method == http.MethodGet {
				path += "?" + req.(string)
			}
		case reflect.Struct:
			fmt.Printf("type:%+v\n", kind)
			var xerr error
			reqJsonBody, xerr = json.Marshal(req)
			if xerr != nil {
				return nil, fmt.Errorf("json.Marshal求数据失败: %v", err)
			}
		case reflect.Slice:
			fmt.Printf("type:%+v\n", kind)
			fmt.Println(req)
			if s, ok := req.([]string); ok {
				/*
					reqSlice := []string{}
					for index, value := range s {
						fmt.Printf("Index: %d, Value: %v\n", index, value)
						valueType := reflect.TypeOf(value).Kind()
						fmt.Println("Value type:", valueType)
						if valueType == reflect.String {
							reflect.ValueOf(value)
							reqSlice = append(reqSlice, fmt.Sprintf("%v", reflect.ValueOf(value)))
						} else {
							return nil, fmt.Errorf("非预期切片类型内部无法处理")
						}
					}
				*/
				if method == http.MethodGet {
					path += "?" + strings.Join(s, "&")
				}
			} else {
				return nil, fmt.Errorf("非预期切片类型无法处理")
			}
		default:
			fmt.Print("def\n", kind)
		}
	}

	isJson := false
	if header != nil {
		for key, value := range header {
			if strings.ContainsAny(value, "json") {
				isJson = true
			}
			fmt.Println("key", key, "val", value)
		}
	}

	fmt.Println(
		"请求第三方信息",
		"path", path, "\t",
		"method", method, "\t",
		"reqJson", string(reqJsonBody), "\t",
		"reqForm", reqFormBody, "\t",
		"isJson", isJson,
	)

	/*
		var request *http.Request
		// 创建 HTTP 请求
		if isJson {
			request, err = http.NewRequest(method, path, bytes.NewBuffer(reqJsonBody))
		} else {
			request, err = http.NewRequest(method, path, bytes.NewBufferString(reqFormBody.Encode()))
		}
		if err != nil {
			return nil, fmt.Errorf("创建 HTTP 请求失败: %v", err)
		}

		if header != nil {
			for key, value := range header {
				request.Header.Set(key, value)
			}
		}

		// 发起 HTTP 请求
		client := &http.Client{}
		resp, err := client.Do(request)
		if err != nil {
			return nil, fmt.Errorf("发起 HTTP 请求失败: %v", err)
		}
		defer resp.Body.Close()

		// 读取响应体
		body, err = io.ReadAll(resp.Body)
		if err != nil {
			return nil, fmt.Errorf("读取响应体失败: %v", err)
		}

		// 检查响应状态码 只要不是正常的200 都是错误
		if resp.StatusCode != http.StatusOK {
			return nil, fmt.Errorf("%s: %s", string(body), resp.Status)
		}
	*/

	// 解析 body 逻辑层自己处理
	return body, nil
}
