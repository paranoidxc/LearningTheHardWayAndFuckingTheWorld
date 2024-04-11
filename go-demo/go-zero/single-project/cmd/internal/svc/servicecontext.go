package svc

import (
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/redis"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"github.com/zeromicro/go-zero/zrpc"
	"gorm.io/gorm/schema"
	"single-project/cmd/internal/config"
	"single-project/cmd/model"
	"single-project/rpc/demo"

	"gorm.io/driver/mysql"

	"gorm.io/gorm"
)

type ServiceContext struct {
	Config         config.Config
	GormDb         *gorm.DB
	Rds            *redis.Redis
	DemoGrpcClient demo.DemoClient

	SysApisModel model.SysApisModel
}

func NewServiceContext(c config.Config) *ServiceContext {

	gormDb, err := gorm.Open(mysql.Open(c.DataSource), &gorm.Config{
		NamingStrategy: schema.NamingStrategy{
			//TablePrefix:   "tech_", // 表名前缀，`User` 的表名应该是 `t_users`
			//SingularTable: true, // 使用单数表名，启用该选项，此时，`User` 的表名应该是 `t_user`
		},
	})

	if err != nil {
		errInfo := fmt.Sprintf("Gorm connect database err:%v", err)
		panic(errInfo)
	}

	// redis create
	rds := redis.MustNewRedis(c.RedisConf)

	// grpc server
	demoGrpcClient := createDemoGrpcClient(c)

	return &ServiceContext{
		Config:         c,
		SysApisModel:   model.NewSysApisModel(sqlx.NewMysql(c.DataSource), c.Cache), // 手动代码
		GormDb:         gormDb,
		Rds:            rds,
		DemoGrpcClient: demoGrpcClient,
	}
}

func createDemoGrpcClient(c config.Config) demo.DemoClient {
	fmt.Printf("DemoTarget %+v\n", c.DemoTarget)
	clientConf := zrpc.RpcClientConf{
		Target: c.DemoTarget,
	}
	conn := zrpc.MustNewClient(clientConf)
	client := demo.NewDemoClient(conn.Conn())

	fmt.Printf("createDemoGrpcClient okay\n")
	return client
	/*
		resp, err := client.Ping(context.Background(), &demo.Request{})
		if err != nil {
			log.Fatal(err)
			return
		}
		log.Println(resp)
	*/
}
