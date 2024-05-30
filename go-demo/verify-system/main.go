package main

import "fmt"

type ThirdPartClienter interface {
	DoAuthUrl()
}

type ThirdPartClient struct {
	cf     ClientConf
	client ThirdPartClienter
}

type ClientConf struct {
	AppId     string
	AppSecret string
}

func NewThirdPartClient(conf ClientConf) *ThirdPartClient {
	var client ThirdPartClienter
	if conf.AppSecret == "dy" {
		client = dyClient{
			conf: conf,
		}
	}

	return &ThirdPartClient{
		cf:     conf,
		client: client,
	}
}

type dyClient struct {
	conf ClientConf
}

func (c dyClient) DoAuthUrl() {
	c.GetToken()
	fmt.Printf("dyClient conf %+v\n", c)
	fmt.Println("dyClient Do real AuthUrl")
}

func (c dyClient) GetToken() {
	fmt.Println("dyClient call GetToken()")
}

type mtClient struct {
	conf ClientConf
}

func (c mtClient) DoAuthUrl() {
	fmt.Println("mtClient DoAuthUrl")
}

func main() {
	conf := ClientConf{
		AppId:     "1",
		AppSecret: "dy",
	}

	clientWrap := NewThirdPartClient(conf)
	clientWrap.client.DoAuthUrl()
}
