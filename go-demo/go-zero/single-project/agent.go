package main

import (
	"flag"
	"fmt"
	"single-project/internal/config"
	"single-project/internal/handler"
	"single-project/internal/svc"

	"github.com/zeromicro/go-zero/core/conf"
	"github.com/zeromicro/go-zero/rest"
)

var configFile = flag.String("f", "etc/agent-api.yaml", "the config file")

func main() {
	flag.Parse()

	var c config.Config
	conf.MustLoad(*configFile, &c)

	server := rest.MustNewServer(c.RestConf)
	defer server.Stop()

	ctx := svc.NewServiceContext(c)
	handler.RegisterHandlers(server, ctx)

	/*
		fmt.Printf("DemoTarget %+v\n", c.DemoTarget)
		clientConf := zrpc.RpcClientConf{
			Target: c.DemoTarget,
		}
		conn := zrpc.MustNewClient(clientConf)
		client := demo.NewDemoClient(conn.Conn())
		resp, err := client.Ping(context.Background(), &demo.Request{})
		if err != nil {
			log.Fatal(err)
			return
		}
		log.Println(resp)
	*/

	fmt.Printf("Starting server at %s:%d...\n", c.Host, c.Port)
	server.Start()
}
