package logic

import (
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"log"
	"single-project/demo"
	"single-project/internal/svc"
)

type PingLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewPingLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PingLogic {
	return &PingLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *PingLogic) Ping() error {
	// todo: add your logic here and delete this line
	// time.Sleep(50 * time.Millisecond)
	fmt.Println("will call grpc")
	resp, err := l.svcCtx.DemoGrpcClient.Ping(l.ctx, &demo.Request{})
	if err != nil {
		log.Fatal(err)
		return nil
	}
	log.Println(resp)
	return nil
}
